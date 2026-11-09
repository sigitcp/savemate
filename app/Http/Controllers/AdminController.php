<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Wallet;
use App\Models\WalletMutation;
use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        // ===== DATA SISWA =====
        $totalSiswa = Student::count();
        $totalSaldoSiswa = Wallet::sum('balance'); // Total saldo seluruh siswa
        
        // ===== DATA KANTIN =====
        $totalKantin = Canteen::count();
        
        // ===== DATA TRANSAKSI (Pembelian di Kantin) =====
        $totalTransaksi = Transaction::where('status', 'success')->count();
        $totalPendapatanKantin = Transaction::where('status', 'success')->sum('total');
        
        $transaksiHariIni = Transaction::where('status', 'success')
            ->whereDate('transaction_at', today())
            ->count();
        $pendapatanHariIni = Transaction::where('status', 'success')
            ->whereDate('transaction_at', today())
            ->sum('total');
        
        // ===== DATA TOPUP (Pengisian Saldo) =====
        $totalTopup = WalletMutation::where('type', 'topup')->count();
        $totalNominalTopup = WalletMutation::where('type', 'topup')->sum('amount');
        
        $topupHariIni = WalletMutation::where('type', 'topup')
            ->whereDate('created_at', today())
            ->count();
        $nominalTopupHariIni = WalletMutation::where('type', 'topup')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        // ===== DATA MUTASI (Topup + Transaksi Hari Ini) =====
        $mutasiHariIni = WalletMutation::whereDate('created_at', today())->count();
        
        // ===== AKTIVITAS TERBARU (5 transaksi + 5 topup terakhir) =====
        $aktivitasTerbaru = WalletMutation::with(['wallet.student', 'transaction.canteen'])
            ->latest()
            ->take(10)
            ->get();
        
        $data = compact(
            'totalSiswa', 'totalSaldoSiswa', 'totalKantin',
            'totalTransaksi', 'totalPendapatanKantin',
            'transaksiHariIni', 'pendapatanHariIni',
            'totalTopup', 'totalNominalTopup',
            'topupHariIni', 'nominalTopupHariIni',
            'mutasiHariIni', 'aktivitasTerbaru'
        );
        
        return view('admin.dashboard', compact('data'));
    }

    // ===== SISWA =====
    public function siswaIndex()
    {
        $students = Student::with(['user', 'wallet'])->latest()->get();
        return view('admin.siswa.index', compact('students'));
    }

    public function siswaCreate()
    {
        return view('admin.siswa.create');
    }

    public function siswaStore(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nis' => 'required|unique:students,nis',
            'name' => 'required',
            'class' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);
    
        // 2. LOGIKA GENERATE TOKEN YANG AMAN (Anti-Duplicate)
        // Ambil semua token yang ada, ambil 3 digit terakhir, cari nilai maksimalnya, lalu +1
        $existingTokens = Student::pluck('rfid_token')->map(function($token) {
            return (int) substr($token, -3);
        });
        
        $nextNumber = $existingTokens->max() + 1;
        $token = 'SC-' . date('Y') . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
        // 3. Logika Upload Foto Langsung ke Folder Public
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/students'), $filename);
            $photoPath = 'uploads/students/' . $filename;
        }
    
        // 4. Buat User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'phone' => $request->phone,
            'is_active' => true,
        ]);
    
        // 5. Buat Student
        $student = Student::create([
            'user_id' => $user->id,
            'nis' => $request->nis,
            'name' => $request->name,
            'class' => $request->class,
            'rfid_token' => $token,
            'photo' => $photoPath,
            'is_active' => true,
        ]);
    
        // 6. Buat Wallet
        Wallet::create([
            'student_id' => $student->id,
            'balance' => 0,
        ]);
    
        return redirect()->route('admin.siswa.index')
            ->with('success', "Siswa berhasil ditambahkan. Token RFID: <strong>{$token}</strong>.");
    }

        // ===== EDIT & UPDATE SISWA =====
        public function siswaUpdate(Request $request, Student $student)
        {
            // Validasi (abaikan unique untuk data yang sedang diedit)
            $request->validate([
                'nis' => 'required|unique:students,nis,' . $student->id,
                'name' => 'required',
                'class' => 'required',
                'email' => 'required|email|unique:users,email,' . $student->user_id,
                'phone' => 'nullable|string|max:20',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'password' => 'nullable|min:6', // Opsional, hanya diisi jika ingin ganti password
            ]);
    
            $user = $student->user;
    
            // 1. Update Data User
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            
            // Hanya update password jika diisi
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
    
            // 2. Update Data Student & Foto
            $photoPath = $student->photo;
            
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($photoPath && file_exists(public_path($photoPath))) {
                    unlink(public_path($photoPath));
                }
                
                // Upload foto baru
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/students'), $filename);
                $photoPath = 'uploads/students/' . $filename;
            }
    
            $student->update([
                'nis' => $request->nis,
                'name' => $request->name,
                'class' => $request->class,
                'photo' => $photoPath,
                // 'rfid_token' TIDAK DIUPDATE (Tetap sama)
            ]);
    
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil diperbarui.');
        }
    public function siswaDestroy(Student $student)
    {
        try {
            // 1. Hapus semua transaksi siswa (cascade akan hapus transaction_items)
            $student->transactions()->delete();
            
            // 2. Hapus semua mutasi saldo di wallet
            if ($student->wallet) {
                $student->wallet->mutations()->delete();
                $student->wallet->delete();
            }
            
            // 3. Hapus data siswa
            $student->delete();
            
            // 4. Hapus user (akun login)
            if ($student->user) {
                $student->user->delete();
            }
            
            // 5. Hapus file foto jika ada
            if ($student->photo && file_exists(public_path($student->photo))) {
                unlink(public_path($student->photo));
            }
    
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Siswa beserta seluruh data terkait berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa.index')
                ->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }

    // ===== TAMBAH SALDO =====
    public function saldoTopup(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $wallet = $student->wallet;
        $wallet->addBalance($request->amount, 'Pengisian saldo oleh Admin');

        return redirect()->route('admin.siswa.index')->with('success', "Saldo berhasil ditambah sebesar Rp " . number_format($request->amount, 0, ',', '.'));
    }

    // ===== KANTIN =====
    public function kantinIndex()
    {
        $canteens = Canteen::with('user')->latest()->get();
        return view('admin.kantin.index', compact('canteens'));
    }

    public function kantinCreate()
    {
        return view('admin.kantin.create');
    }

    public function kantinStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'owner' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->owner . ' - ' . $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'kantin',
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        Canteen::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'owner' => $request->owner,
            'location' => $request->location,
            'is_active' => true,
        ]);

        return redirect()->route('admin.kantin.index')->with('success', 'Kantin berhasil ditambahkan.');
    }

    public function kantinDestroy(Canteen $canteen)
    {
        $canteen->user->delete();
        return redirect()->route('admin.kantin.index')->with('success', 'Kantin berhasil dihapus.');
    }

    // ===== EDIT & UPDATE KANTIN =====
    public function kantinUpdate(Request $request, Canteen $canteen)
    {
        // Validasi (abaikan unique untuk email yang sedang diedit)
        $request->validate([
            'name' => 'required',
            'owner' => 'required',
            'location' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $canteen->user_id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6', // Opsional, hanya diisi jika ingin ganti password
        ]);

        $user = $canteen->user;

        // 1. Update Data User
        $user->name = $request->owner . ' - ' . $request->name; // Pertahankan format nama user
        $user->email = $request->email;
        $user->phone = $request->phone;
        
        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // 2. Update Data Canteen
        $canteen->update([
            'name' => $request->name,
            'owner' => $request->owner,
            'location' => $request->location,
        ]);

        return redirect()->route('admin.kantin.index')
            ->with('success', 'Data kantin berhasil diperbarui.');
    }
    
    // ===== MENU =====
    public function menuIndex()
    {
        $menus = Menu::with('canteen')->latest()->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function menuCreate()
    {
        $canteens = Canteen::where('is_active', true)->get();
        return view('admin.menu.create', compact('canteens'));
    }

    public function menuStore(Request $request)
    {
        $request->validate([
            'canteen_id' => 'required|exists:canteens,id',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'category' => 'required',
        ]);

        Menu::create($request->all() + ['is_active' => true]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function menuDestroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus.');
    }

    public function laporan()
    {
        // Perbaikan: pisahkan 'canteen' dan 'items.menu'
        $transactions = Transaction::with(['student', 'canteen', 'items.menu']) 
            ->latest('transaction_at')
            ->paginate(20);
            
        return view('admin.laporan', compact('transactions'));
    }
}