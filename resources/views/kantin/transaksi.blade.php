@extends('layouts.app')
@section('title', 'Transaksi')

@section('sidebar-menu')
    <a href="{{ route('kantin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('kantin.transaksi') }}" class="active"><i class="bi bi-cart-check"></i> Transaksi</a>
    <a href="{{ route('kantin.riwayat') }}"><i class="bi bi-clock-history"></i> Riwayat</a>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Transaksi</h3>
        <span class="badge bg-success fs-6"><i class="bi bi-wifi"></i> Mode Auto RFID</span>
    </div>

    <div class="row g-3">
        <!-- Daftar Menu -->
        <div class="col-12 col-md-7 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="bi bi-grid-3x3-gap"></i> Pilih Menu</h5>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div class="row g-2" id="menuList">
                        @foreach($menus as $menu)
                        <div class="col-6 col-sm-4 col-md-6 col-lg-4">
                            <div class="card menu-card p-2 p-md-3 text-center h-100 border-0 shadow-sm" 
                                 data-id="{{ $menu->id }}" data-name="{{ $menu->name }}" data-price="{{ $menu->price }}">
                                <h6 class="mb-1 fw-bold text-dark">{{ $menu->name }}</h6>
                                <small class="text-primary fw-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="col-12 col-md-5 col-lg-4">
            <div class="card shadow-sm border-0 position-sticky" style="top: 20px; z-index: 100;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="bi bi-cart3"></i> Keranjang</h5>
                </div>
                <div class="card-body p-0">
                    <div id="cartItems" style="min-height: 150px; max-height: 40vh; overflow-y: auto; padding: 1rem;">
                        <p class="text-muted text-center mt-4 small">Belum ada menu dipilih</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-semibold">TOTAL:</span>
                        <strong id="totalAmount" class="fs-4 text-primary">Rp 0</strong>
                    </div>
                    <div class="d-grid gap-2">
                        <button id="btnBayar" class="btn btn-primary btn-lg fw-bold" disabled><i class="bi bi-credit-card"></i> Bayar Sekarang</button>
                        <button id="btnReset" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Kosongkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Auto RFID (Tanpa Input Manual) -->
    <div class="modal fade" id="rfidModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6"><i class="bi bi-wifi"></i> Menunggu Tap Kartu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resetRfid()"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div id="rfidLoading">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="fw-bold text-primary mb-2">Silakan Tempelkan Kartu</h5>
                        <p class="text-muted small">Sistem akan otomatis membaca.</p>
                    </div>
                    <div id="rfidResult" style="display:none;"></div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0" id="rfidFooter" style="display:none;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="resetRfid()"><i class="bi bi-x-circle"></i> Batal</button>
                    <button type="button" class="btn btn-success px-4" id="btnLanjut" onclick="prosesTransaksi()"><i class="bi bi-check-circle"></i> Lanjut</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let cart = {};
let selectedStudentId = null;
let nfcBuffer = ""; // Buffer untuk menampung ketikan dari alat RFID

document.querySelectorAll('.menu-card').forEach(card => {
    card.addEventListener('click', () => {
        card.style.transform = "scale(0.95)"; setTimeout(() => card.style.transform = "scale(1)", 100);
        const id = card.dataset.id, name = card.dataset.name, price = parseInt(card.dataset.price);
        if (cart[id]) cart[id].qty++; else cart[id] = { id, name, price, qty: 1 };
        renderCart();
    });
});

function renderCart() {
    const container = document.getElementById('cartItems');
    const items = Object.values(cart);
    if (items.length === 0) {
        container.innerHTML = '<p class="text-muted text-center mt-4 small">Belum ada menu dipilih</p>';
        document.getElementById('totalAmount').textContent = 'Rp 0';
        document.getElementById('btnBayar').disabled = true;
        return;
    }
    let html = '', total = 0;
    items.forEach(item => {
        const subtotal = item.price * item.qty; total += subtotal;
        html += `<div class="d-flex justify-content-between align-items-start mb-2 p-2 bg-light rounded">
            <div class="text-start flex-grow-1"><strong class="small d-block">${item.name}</strong><small class="text-muted">Rp ${item.price.toLocaleString('id-ID')} × ${item.qty}</small></div>
            <div class="text-end ms-2"><span class="d-block fw-bold small text-primary">Rp ${subtotal.toLocaleString('id-ID')}</span>
            <button class="btn btn-sm btn-outline-danger py-0 px-1 mt-1" onclick="removeItem(${item.id})"><i class="bi bi-x"></i></button></div></div>`;
    });
    container.innerHTML = html;
    document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('btnBayar').disabled = false;
}

function removeItem(id) { delete cart[id]; renderCart(); }
document.getElementById('btnReset').addEventListener('click', () => { if(confirm('Kosongkan keranjang?')) { cart = {}; renderCart(); } });

document.getElementById('btnBayar').addEventListener('click', () => {
    const modal = new bootstrap.Modal(document.getElementById('rfidModal'));
    modal.show(); resetRfid();
});

// FITUR UTAMA: Global Key Listener untuk Alat RFID
document.addEventListener('keydown', function(e) {
    if (document.getElementById('rfidModal').classList.contains('show')) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (nfcBuffer.trim().length > 3) {
                checkToken(nfcBuffer.trim());
                nfcBuffer = "";
            }
        } else if (e.key.length === 1) {
            nfcBuffer += e.key;
        }
    }
});

function checkToken(token) {
    const total = Object.values(cart).reduce((sum, item) => sum + (item.price * item.qty), 0);
    const items = Object.values(cart).map(item => ({ menu_id: item.id, qty: item.qty }));

    document.getElementById('rfidLoading').style.display = 'none';
    const result = document.getElementById('rfidResult');
    result.style.display = 'block';
    result.innerHTML = '<div class="spinner-border text-primary mb-2"></div><p>Memverifikasi...</p>';

    fetch('{{ route("kantin.cekToken") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ token, total, items })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            result.innerHTML = `<div class="alert alert-danger border-danger mb-0"><i class="bi bi-x-octagon-fill fs-1 d-block mb-2"></i><h6 class="fw-bold">Gagal!</h6><p class="mb-0 small">${data.message}</p></div>
            <button class="btn btn-secondary w-100 mt-3" data-bs-dismiss="modal" onclick="resetRfid()">Tutup / Coba Lagi</button>`;
            return;
        }

        selectedStudentId = data.student.id;
        let photoHtml = data.student.photo 
            ? `<img src="${data.student.photo}" class="rounded-circle border border-3 border-success mb-2 shadow-sm" style="width:80px;height:80px;object-fit:cover;">`
            : `<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width:80px;height:80px;font-size:2rem;font-weight:bold;">${data.student.name.charAt(0)}</div>`;

        result.innerHTML = `<div class="alert alert-success border-success mb-0">
            <i class="bi bi-check-circle-fill fs-1 d-block mb-2 text-success"></i>
            <h6 class="fw-bold mb-2">Data Siswa Ditemukan</h6>
            ${photoHtml}
            <h5 class="mb-1 fw-bold text-dark">${data.student.name}</h5>
            <p class="mb-2">Kelas: <span class="badge bg-info text-dark">${data.student.class}</span></p>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Total Belanja:</span>
                <strong class="text-danger fs-5">Rp ${total.toLocaleString('id-ID')}</strong>
            </div>
        </div>`;
        document.getElementById('rfidFooter').style.display = 'flex';
    })
    .catch(err => { 
        result.innerHTML = `<div class="alert alert-danger mb-0">Error Koneksi</div><button class="btn btn-secondary w-100 mt-3" onclick="resetRfid()">Coba Lagi</button>`; 
    });
}

function prosesTransaksi() {
    const total = Object.values(cart).reduce((sum, item) => sum + (item.price * item.qty), 0);
    const items = Object.values(cart).map(item => ({ menu_id: item.id, qty: item.qty }));
    let itemsInputs = '';
    items.forEach((item, index) => {
        itemsInputs += `<input type="hidden" name="items[${index}][menu_id]" value="${item.menu_id}">`;
        itemsInputs += `<input type="hidden" name="items[${index}][qty]" value="${item.qty}">`;
    });

    const form = document.createElement('form');
    form.method = 'POST'; form.action = '{{ route("kantin.prosesTransaksi") }}';
    form.innerHTML = `@csrf <input type="hidden" name="student_id" value="${selectedStudentId}"><input type="hidden" name="total" value="${total}">${itemsInputs}`;
    document.body.appendChild(form); form.submit();
}

function resetRfid() {
    nfcBuffer = "";
    document.getElementById('rfidLoading').style.display = 'block';
    document.getElementById('rfidResult').style.display = 'none';
    document.getElementById('rfidFooter').style.display = 'none';
}
</script>
@endpush