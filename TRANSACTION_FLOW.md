# 📊 ALUR TRANSAKSI SISTEM SIBERIKAN

## Sistem 2-Level Transaksi

Sistem Siberikan memiliki **2 jenis transaksi** yang berbeda:

### 🐟 **Level 1: Transaksi Nelayan → Tengkulak** (PENAWARAN)

**Model:** `Penawaran`
**Status:** `draft` → `pending` → `approved` / `rejected`

#### Flow:
1. **Nelayan** membuat penawaran ikan
   - Akses: `/dashboard/nelayan/penawaran/create`
   - Input: Jenis Ikan, Jumlah (kg), Harga/Kg, Kualitas, Lokasi, Kedalaman, Tanggal Tangkap, Foto
   - Status dibuat: `draft`

2. **Nelayan** submit penawaran
   - Button: "Submit Penawaran"
   - Status berubah: `draft` → `pending`

3. **Tengkulak** review penawaran
   - Dashboard: Section "Penawaran Menunggu Persetujuan"
   - Action: Approve atau Reject
   - Approve: Status `pending` → `approved`
   - Reject: Status `pending` → `rejected`

4. **Penawaran Approved** siap untuk dijual
   - Tampil di: Section "Penawaran Sudah Disetujui"
   - Stok tersedia untuk dibeli pembeli

---

### 🛒 **Level 2: Transaksi Tengkulak → Pembeli** (TRANSAKSI)

**Model:** `Transaksi` + `DetailTransaksi`
**Status:** `pending` → `dikemas` → `dikirim` → `selesai` / `dibatalkan`

#### Flow:
1. **Pembeli** browse ikan yang approved
   - Akses: `/dashboard/pembeli/browse`
   - Hanya menampilkan Penawaran dengan status `approved`
   - Lihat: Jenis Ikan, Harga/Kg, Foto, Nelayan, Lokasi, Kedalaman

2. **Pembeli** order ikan
   - Button: "Pesan Ikan" pada detail
   - Form: Jumlah order (kg), Catatan
   - Input harus: `jumlah ≤ penawaran.jumlah_kg`

3. **Sistem** buat Transaksi
   - Model: `Transaksi`
   - Fields: `kode_transaksi`, `tengkulak_id` (nelayan), `pembeli_id`, `status: pending`, `total_harga`
   - Buat DetailTransaksi dengan `hasil_tangkapan_id = penawaran.id`

4. **Kurangi Stok**
   - Update: `penawaran.jumlah_kg -= jumlah_order`
   - Jika habis: `penawaran.status = sold_out`

5. **Pembeli** track order
   - Dashboard: Riwayat Pembelian
   - Status flow: pending → dikemas → dikirim → selesai

---

## 📦 Model & Database

### Penawaran Table
```
id | kode_penawaran | nelayan_id | jenis_ikan_id | jumlah_kg | harga_per_kg | 
kualitas | lokasi_tangkapan | kedalaman | tanggal_tangkapan | foto_ikan | 
status | alasan_reject | approved_by | approved_at | created_at
```

**Status Values:**
- `draft` - Baru dibuat, belum submit
- `pending` - Menunggu approval dari tengkulak
- `approved` - Sudah disetujui, siap dijual
- `rejected` - Ditolak oleh tengkulak
- `sold_out` - Stok habis terjual semua
- `canceled` - Dibatalkan oleh nelayan

### Transaksi Table
```
id | kode_transaksi | tengkulak_id | pembeli_id | tanggal_transaksi | 
total_harga | status | catatan | created_at
```

**Status Values:**
- `pending` - Baru order, menunggu konfirmasi
- `dikemas` - Sedang dikemas oleh penjual
- `dikirim` - Sudah dikirim oleh sopir
- `selesai` - Diterima pembeli
- `dibatalkan` - Order dibatalkan

---

## 🎯 User Roles & Access

| Role | Akses Level 1 (Penawaran) | Akses Level 2 (Transaksi) |
|------|--------------------------|---------------------------|
| **Nelayan** | ✅ Create, Submit, Edit Penawaran | ❌ Tidak | 
| **Tengkulak** | ✅ Review, Approve, Reject | ✅ Terima order, Manage pengiriman |
| **Pembeli** | ❌ Tidak | ✅ Browse, Order, Track |
| **Sopir** | ❌ Tidak | ✅ Update pengiriman status |

---

## 🔄 Data Flow Diagram

```
┌─────────────┐
│   Nelayan   │
└──────┬──────┘
       │ 1. Create Penawaran (draft)
       │ 2. Submit Penawaran (pending)
       ↓
┌──────────────────────────┐
│ Penawaran (status:pending)│
└──────┬───────────────────┘
       │ 3. Review by Tengkulak
       ↓
   ┌───────────────┐
   │ Approve/Reject│
   └───┬───────┬───┘
       │       │
   Approve  Reject
       │       │
       ↓       ↓
   ┌─────┐  ┌────────┐
   │app. │  │rejected │
   └──┬──┘  └────────┘
      │
      │ 4. Pembeli browse
      ↓
┌──────────────┐
│   Pembeli    │
└──────┬───────┘
       │ 5. Order Ikan
       ↓
┌──────────────────────┐
│ Transaksi (pending)  │
│ ↓ Kurangi stok       │
└──────┬───────────────┘
       │ 6. Tengkulak pack
       ↓
┌──────────────────────┐
│ Transaksi (dikemas)  │
└──────┬───────────────┘
       │ 7. Sopir deliver
       ↓
┌──────────────────────┐
│ Transaksi (dikirim)  │
└──────┬───────────────┘
       │ 8. Pembeli terima
       ↓
┌──────────────────────┐
│ Transaksi (selesai)  │
└──────────────────────┘
```

---

## 📍 URL Reference

### Nelayan - Penawaran Creation
- Create Form: `/dashboard/nelayan/penawaran/create`
- List Penawarans: `/dashboard/nelayan/penawarans`
- Detail: `/dashboard/nelayan/penawaran/{id}`
- Edit: `/dashboard/nelayan/penawaran/{id}/edit`
- Submit: `POST /dashboard/nelayan/penawaran/{id}/submit`

### Tengkulak - Penawaran Approval
- Dashboard: `/dashboard/tengkulak`
- List Pending: `/dashboard/tengkulak/penawarans/pending`
- Detail Approval: `/dashboard/tengkulak/penawaran/{id}/approval`
- Approve: `POST /dashboard/tengkulak/penawaran/{id}/approve`
- Reject: `POST /dashboard/tengkulak/penawaran/{id}/reject`

### Pembeli - Shopping
- Browse: `/dashboard/pembeli/browse`
- Detail: `/dashboard/pembeli/ikan/{id}`
- Create Order: `POST /dashboard/pembeli/order`
- Dashboard: `/dashboard/pembeli`
- My Orders: `/dashboard/pembeli` (dalam dashboard)

---

## ✅ Testing Checklist

- [ ] Nelayan bisa buat penawaran
- [ ] Nelayan bisa submit penawaran (status: pending)
- [ ] Tengkulak lihat "Penawaran Menunggu Persetujuan"
- [ ] Tengkulak bisa approve penawaran
- [ ] Penawaran approved muncul di "Penawaran Sudah Disetujui"
- [ ] Pembeli browse ikan approved
- [ ] Pembeli bisa order ikan
- [ ] Stok berkurang setelah order
- [ ] Transaksi muncul di pembeli dashboard
- [ ] Tengkulak bisa manage pengiriman

---

**Last Updated:** 16 December 2025
**System:** SIBERIKAN v1.0
