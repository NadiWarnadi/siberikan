<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NelayanController;
use App\Http\Controllers\TengkulakController;
use App\Http\Controllers\SopirController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\SuratPengirimanController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DeliveryManagementController;
use App\Http\Controllers\PembeliShoppingController;
use App\Http\Controllers\OwnerOrderApprovalController;

// Welcome page
Route::get('/', function () {
    return view('welcome-new');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard Routes - Protected by auth
Route::middleware('auth')->group(function () {
    
    // Nelayan Dashboard
    Route::middleware('role:nelayan')->group(function () {
        Route::get('/dashboard/nelayan', [NelayanController::class, 'index'])->name('nelayan.dashboard');
        Route::post('/dashboard/nelayan/tangkapan', [NelayanController::class, 'store'])->name('nelayan.tangkapan.store');
        Route::put('/dashboard/nelayan/tangkapan/{id}', [NelayanController::class, 'update'])->name('nelayan.tangkapan.update');
        Route::delete('/dashboard/nelayan/tangkapan/{id}', [NelayanController::class, 'destroy'])->name('nelayan.tangkapan.destroy');
        
        // Penawaran routes
        Route::get('/dashboard/nelayan/penawaran/create', [\App\Http\Controllers\PenawaranController::class, 'showCreateForm'])->name('nelayan.create-penawaran-form');
        Route::post('/dashboard/nelayan/penawaran/create', [\App\Http\Controllers\PenawaranController::class, 'createPenawaran'])->name('nelayan.create-penawaran');
        Route::get('/dashboard/nelayan/penawarans', [\App\Http\Controllers\PenawaranController::class, 'listPenawaranNelayan'])->name('nelayan.list-penawarans');
        Route::get('/dashboard/nelayan/penawaran/{id}', [\App\Http\Controllers\PenawaranController::class, 'detailPenawaran'])->name('nelayan.detail-penawaran');
        Route::put('/dashboard/nelayan/penawaran/{id}/edit', [\App\Http\Controllers\PenawaranController::class, 'editPenawaran'])->name('nelayan.edit-penawaran');
        Route::post('/dashboard/nelayan/penawaran/{id}/submit', [\App\Http\Controllers\PenawaranController::class, 'submitPenawaran'])->name('nelayan.submit-penawaran');
        Route::post('/dashboard/nelayan/penawaran/{id}/cancel', [\App\Http\Controllers\PenawaranController::class, 'cancelPenawaran'])->name('nelayan.cancel-penawaran');
    });

    // Tengkulak Dashboard
    Route::middleware('role:tengkulak')->group(function () {
        Route::get('/dashboard/tengkulak', [\App\Http\Controllers\TengkulakApprovalController::class, 'dashboard'])->name('tengkulak.dashboard');
        
        // Approval routes
        Route::get('/dashboard/tengkulak/penawarans/pending', [\App\Http\Controllers\TengkulakApprovalController::class, 'listPenawaranPending'])->name('tengkulak.list-penawaran-pending');
        Route::get('/dashboard/tengkulak/penawaran/{id}/approval', [\App\Http\Controllers\TengkulakApprovalController::class, 'detailPenawaranApproval'])->name('tengkulak.detail-penawaran-approval');
        Route::post('/dashboard/tengkulak/penawaran/{id}/approve', [\App\Http\Controllers\TengkulakApprovalController::class, 'approvePenawaran'])->name('tengkulak.approve-penawaran');
        Route::post('/dashboard/tengkulak/penawaran/{id}/reject', [\App\Http\Controllers\TengkulakApprovalController::class, 'rejectPenawaran'])->name('tengkulak.reject-penawaran');
        Route::get('/dashboard/tengkulak/penawaran/{id}/invoice', [\App\Http\Controllers\TengkulakApprovalController::class, 'generateInvoice'])->name('tengkulak.generate-invoice');
        Route::get('/dashboard/tengkulak/history/approved', [\App\Http\Controllers\TengkulakApprovalController::class, 'historyApproved'])->name('tengkulak.history-approved');
        Route::get('/dashboard/tengkulak/history/rejected', [\App\Http\Controllers\TengkulakApprovalController::class, 'historyRejected'])->name('tengkulak.history-rejected');
    });

    // Sopir Dashboard
    Route::middleware('role:sopir')->group(function () {
        Route::get('/dashboard/sopir', [SopirController::class, 'index'])->name('sopir.dashboard');
        Route::get('/dashboard/sopir/surat', [SuratPengirimanController::class, 'listSuratSopir'])->name('sopir.list-surat');
        Route::get('/dashboard/sopir/surat/{nomorResi}', [SuratPengirimanController::class, 'viewSurat'])->name('sopir.view-surat');
        Route::get('/dashboard/sopir/surat/{nomorResi}/download', [SuratPengirimanController::class, 'downloadSurat'])->name('sopir.download-surat');
        Route::put('/dashboard/sopir/pengiriman/{id}/status', [SopirController::class, 'updateStatus'])->name('sopir.pengiriman.status');
        Route::post('/dashboard/sopir/pengiriman/{id}/bukti', [SopirController::class, 'storeBukti'])->name('sopir.pengiriman.bukti');
    });

    // Pembeli Dashboard
    Route::middleware('role:pembeli')->group(function () {
        Route::get('/dashboard/pembeli', [PembeliController::class, 'index'])->name('pembeli.dashboard');
        Route::get('/dashboard/pembeli/browse', [PembeliController::class, 'browse'])->name('pembeli.browse');
        Route::get('/dashboard/pembeli/ikan/{id}', [PembeliController::class, 'detail'])->name('pembeli.detail-ikan');
        Route::post('/dashboard/pembeli/order', [PembeliController::class, 'createOrder'])->name('pembeli.create-order');
        Route::post('/dashboard/pembeli/pengiriman/{id}/konfirmasi', [PembeliController::class, 'confirmReceipt'])->name('pembeli.konfirmasi');
        Route::post('/dashboard/pembeli/retur', [PembeliController::class, 'submitRetur'])->name('pembeli.retur');
    });

    // Admin User Management
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [\App\Http\Controllers\AdminUserController::class, 'showCreateForm'])->name('admin.users.create');
        Route::post('/admin/users', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{id}/edit', [\App\Http\Controllers\AdminUserController::class, 'showEditForm'])->name('admin.users.edit');
        Route::put('/admin/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('admin.users.update');
        Route::get('/admin/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'show'])->name('admin.users.show');
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/admin/users/{id}/toggle-status', [\App\Http\Controllers\AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::get('/admin/users-stats', [\App\Http\Controllers\AdminUserController::class, 'getUserStats'])->name('admin.users.stats');
    });

    // Admin Delivery Management
    Route::middleware('role:admin|staff|owner')->group(function () {
        Route::get('/admin/deliveries', [\App\Http\Controllers\DeliveryManagementController::class, 'index'])->name('admin.deliveries.index');
        Route::get('/admin/deliveries/{id}', [\App\Http\Controllers\DeliveryManagementController::class, 'show'])->name('admin.deliveries.show');
        Route::post('/admin/deliveries/{id}/assign-sopir', [\App\Http\Controllers\DeliveryManagementController::class, 'assignSopir'])->name('admin.deliveries.assign-sopir');
        Route::patch('/admin/deliveries/{id}/status', [\App\Http\Controllers\DeliveryManagementController::class, 'updateStatus'])->name('admin.deliveries.update-status');
        Route::post('/admin/deliveries/{id}/bukti', [\App\Http\Controllers\DeliveryManagementController::class, 'uploadBukti'])->name('admin.deliveries.bukti');
        Route::get('/admin/sopirs/stats', [\App\Http\Controllers\DeliveryManagementController::class, 'getSopirStats'])->name('admin.sopirs.stats');
    });

    // Pembeli Shopping
    Route::middleware('role:pembeli')->group(function () {
        Route::get('/pembeli/browse-fish', [\App\Http\Controllers\PembeliShoppingController::class, 'browse'])->name('pembeli.browse-fish');
        Route::get('/pembeli/fish/{id}', [\App\Http\Controllers\PembeliShoppingController::class, 'detail'])->name('pembeli.fish-detail');
        Route::post('/pembeli/cart/add', [\App\Http\Controllers\PembeliShoppingController::class, 'addToCart'])->name('pembeli.cart.add');
        Route::get('/pembeli/cart', [\App\Http\Controllers\PembeliShoppingController::class, 'viewCart'])->name('pembeli.cart.view');
        Route::patch('/pembeli/cart/update', [\App\Http\Controllers\PembeliShoppingController::class, 'updateCart'])->name('pembeli.cart.update');
        Route::delete('/pembeli/cart/{ikan_id}', [\App\Http\Controllers\PembeliShoppingController::class, 'removeFromCart'])->name('pembeli.cart.remove');
        Route::post('/pembeli/checkout', [\App\Http\Controllers\PembeliShoppingController::class, 'checkout'])->name('pembeli.checkout');
        Route::get('/pembeli/orders', [\App\Http\Controllers\PembeliShoppingController::class, 'myOrders'])->name('pembeli.orders');
    });

    // Owner Order Approval
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/orders/pending', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'pendingOrders'])->name('owner.orders.pending');
        Route::get('/owner/orders/{id}', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'showOrder'])->name('owner.orders.show');
        Route::post('/owner/orders/{id}/approve', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'approve'])->name('owner.orders.approve');
        Route::post('/owner/orders/{id}/reject', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'reject'])->name('owner.orders.reject');
        Route::get('/owner/orders/approved', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'approvedOrders'])->name('owner.orders.approved');
        Route::get('/owner/orders/history', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'orderHistory'])->name('owner.orders.history');
        Route::get('/owner/stats', [\App\Http\Controllers\OwnerOrderApprovalController::class, 'getStats'])->name('owner.stats');
    });
});
