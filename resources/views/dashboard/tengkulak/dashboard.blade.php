@extends('layouts.app')

@section('title', 'Dashboard Tengkulak - SIBERIKAN')

@section('content')
<style>
    :root {
        --primary: #0d6efd;
        --success: #198754;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #0dcaf0;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary), #0056b3);
        color: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card.warning { background: linear-gradient(135deg, var(--warning), #ffb700); color: #333; }
    .stat-card.success { background: linear-gradient(135deg, var(--success), #145a32); color: white; }
    .stat-card.danger { background: linear-gradient(135deg, var(--danger), #bb2d3b); color: white; }
    .stat-card.info { background: linear-gradient(135deg, var(--info), #0aa2c0); color: white; }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        position: relative;
        z-index: 1;
    }

    .stat-label {
        font-size: 0.95rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.2;
        position: absolute;
        top: 10px;
        right: 15px;
    }

    .penawaran-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        background: white;
    }

    .penawaran-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }

    .btn-action {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: transparent;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .image-thumbnail {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        max-height: 250px;
        object-fit: cover;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2"><i class="bi bi-shop"></i> Dashboard Tengkulak</h1>
            <p class="text-muted">Kelola penawaran ikan dari nelayan dan buat approval dengan mudah</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-number" id="countPending">0</div>
                <div class="stat-label">Penawaran Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-number" id="countApproved">0</div>
                <div class="stat-label">Penawaran Approved</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card danger">
                <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
                <div class="stat-number" id="countRejected">0</div>
                <div class="stat-label">Penawaran Rejected</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
                <div class="stat-number" id="countTotal">0</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
    </div>

    <!-- Nav Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" 
                            data-tab="pending-content" type="button" role="tab">
                        <i class="bi bi-hourglass-split"></i> Penawaran Pending
                        <span class="badge bg-warning ms-2" id="badgePending">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" 
                            data-tab="approved-content" type="button" role="tab">
                        <i class="bi bi-check-circle"></i> Approved
                        <span class="badge bg-success ms-2" id="badgeApproved">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" 
                            data-tab="rejected-content" type="button" role="tab">
                        <i class="bi bi-x-circle"></i> Rejected
                        <span class="badge bg-danger ms-2" id="badgeRejected">0</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <!-- Pending Content -->
            <div id="pending-content" class="tab-content active">
                <h5 class="mb-3">Penawaran Menunggu Persetujuan</h5>
                <div id="pendingContainer">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                        <p class="text-muted">Tidak ada penawaran pending</p>
                    </div>
                </div>
            </div>

            <!-- Approved Content -->
            <div id="approved-content" class="tab-content">
                <h5 class="mb-3">Penawaran yang Telah Disetujui</h5>
                <div id="approvedContainer">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
                        <p class="text-muted">Tidak ada penawaran yang disetujui</p>
                    </div>
                </div>
            </div>

            <!-- Rejected Content -->
            <div id="rejected-content" class="tab-content">
                <h5 class="mb-3">Penawaran yang Ditolak</h5>
                <div id="rejectedContainer">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-x-circle"></i></div>
                        <p class="text-muted">Tidak ada penawaran yang ditolak</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load stats
    loadStats();

    // Load penawarans
    loadPenawarans();

    // Tab switching
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelector('#' + this.getAttribute('data-tab')).classList.add('active');
        });
    });
});

function loadStats() {
    fetch('{{ route("tengkulak.dashboard") }}?json=stats')
        .then(r => r.json())
        .then(data => {
            document.getElementById('countPending').textContent = data.pending || 0;
            document.getElementById('countApproved').textContent = data.approved || 0;
            document.getElementById('countRejected').textContent = data.rejected || 0;
            document.getElementById('countTotal').textContent = data.total || 0;
            document.getElementById('badgePending').textContent = data.pending || 0;
            document.getElementById('badgeApproved').textContent = data.approved || 0;
            document.getElementById('badgeRejected').textContent = data.rejected || 0;
        });
}

function loadPenawarans() {
    fetch('{{ route("tengkulak.list-penawaran-pending") }}')
        .then(r => r.text())
        .then(html => {
            // Parse dan display penawarans
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const penawaran = doc.querySelectorAll('[data-penawaran]');
            
            if (penawaran.length === 0) {
                document.getElementById('pendingContainer').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                        <p class="text-muted">Tidak ada penawaran pending</p>
                    </div>
                `;
            }
        });
}

function approvePenawaran(penawaranId) {
    if (!confirm('Setujui penawaran ini? Ikan akan masuk ke stok.')) return;

    fetch('{{ route("tengkulak.approve-penawaran", ":id") }}'.replace(':id', penawaranId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Redirect to invoice
            window.location.href = data.invoice_url;
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function rejectPenawaran(penawaranId) {
    const alasan = prompt('Masukkan alasan penolakan:');
    if (!alasan) return;

    if (alasan.length < 10) {
        alert('Alasan minimal 10 karakter');
        return;
    }

    fetch('{{ route("tengkulak.reject-penawaran", ":id") }}'.replace(':id', penawaranId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ alasan_reject: alasan })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}
</script>
@endsection
