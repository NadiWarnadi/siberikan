# Quick Reference - Security & Responsive Testing

## 🔐 Security Input Testing Commands

### Test 1: XSS Prevention (Browse Filtering)
```
URL: /pembeli/browse?search=<script>alert('xss')</script>
Expected: ✅ Search sanitized, displayed as plain text
Location: resources/views/dashboard/pembeli/browse.blade.php (Blade auto-escape)
```

### Test 2: SQL Injection (Order Creation)
```
POST /pembeli/create-order
Data: jumlah = "1; DROP TABLE penawarans;--"
Expected: ✅ Rejected as non-numeric value
Validator: jumlah|numeric|regex:^\d+(\.\d{1,2})?$
```

### Test 3: File Upload - Wrong Format
```
POST /nelayan/create-penawaran
Upload: malicious.exe renamed to .jpg
Expected: ✅ Rejected - MIME type check fails
Validator: foto_ikan|image|mimes:jpeg,png,jpg|max:5120
+ Code verification: in_array($file->getMimeType(), [...])
```

### Test 4: File Upload - Too Small
```
POST /nelayan/create-penawaran
Upload: 100x100.jpg
Expected: ✅ Rejected - below min 300x300
Validator: dimensions:min_width=300,min_height=300
```

### Test 5: Price Tampering
```
POST /nelayan/create-penawaran
harga_per_kg = 999999999999
Expected: ✅ Rejected - exceeds max:999999
Validator: harga_per_kg|integer|between:1000,999999
```

### Test 6: Unauthorized Approval
```
User: pembeli (non-tengkulak)
Action: Approve penawaran via direct API call
Expected: ✅ abort(403) - Unauthorized
Code: if (Auth::user()->peran !== 'tengkulak') abort(403)
```

### Test 7: Rapid Submission
```
Action: Submit order twice within 60 seconds
Expected: ✅ Second submission rejected
Code: Transaksi::where('pembeli_id', Auth::id())
      ->where('created_at', '>=', now()->subMinutes(1))->exists()
```

### Test 8: Stock Race Condition
```
Concurrent: Order same item from 2 browsers
Stok: 10kg available
Expected: ✅ One succeeds, one fails with "stok telah berubah"
Code: lockForUpdate() prevents overselling
```

---

## 📱 Responsive Design Testing

### Mobile Testing (375px - 425px)
```
URL: /pembeli/browse
1. [ ] Product cards display 1 per row
2. [ ] Filter buttons stack vertically
3. [ ] Order modal fits screen without scrolling
4. [ ] Quantity +/- buttons are touch-friendly (44px)
5. [ ] Product price displays correctly
6. [ ] Images load without distortion
```

### Tablet Testing (768px - 1024px)
```
URL: /pembeli/browse
1. [ ] Product cards display 2 per row
2. [ ] Filter inputs show 2 per row
3. [ ] Order modal displays properly
4. [ ] Sticky order form not sticky (static position)
```

### Desktop Testing (1920px+)
```
URL: /pembeli/browse
1. [ ] Product cards display 3-4 per row
2. [ ] Filter section in single row
3. [ ] Pagination works

URL: /pembeli/detail/{id}
1. [ ] Order form sticky on right side
2. [ ] Product specs 6 in grid (2x3)
3. [ ] Image displays at good size
4. [ ] All info readable without scrolling
```

### Touch Interaction Testing
```
1. [ ] Quantity +/- buttons respond on first tap
2. [ ] Modal close button is easy to tap
3. [ ] Filter dropdowns open with single tap
4. [ ] No horizontal scroll on any page
```

---

## 🔍 Validation Error Messages

### Expected Error Messages When Testing

| Input | Expected Error |
|-------|-----------------|
| Empty search field | Works normally (optional) |
| `search=<script>` | Rejected by regex |
| `jenis_ikan=999` | "Jenis ikan tidak valid" |
| `nelayan=0` | "Nelayan tidak ditemukan" |
| `jumlah=abc` | "Jumlah kg harus berupa angka" |
| `jumlah=0` | "Jumlah harus antara 0.1..." |
| `harga=500` | "Harga harus antara Rp 1.000..." |
| `harga=2000000` | "Harga harus antara Rp 1.000..." |
| No image upload | "Foto ikan harus diunggah" |
| 50x50.jpg | "Ukuran gambar minimal 300x300 pixel" |
| file.gif | "Format gambar harus JPEG atau PNG" |
| 20MB.jpg | "Ukuran gambar maksimal 5MB" |

---

## 📊 Database Testing

### Verify Grade Mapping Works
```sql
-- Check if hasil_tangkapan created with correct grade
SELECT ht.id, ht.grade, p.kualitas 
FROM hasil_tangkapans ht
JOIN penawarans p ON ht.penawaran_id = p.id
WHERE p.kualitas IN ('premium', 'standar', 'ekonomi');

-- Expected: premium=A, standar=B, ekonomi=C
```

### Verify Stock Updates
```sql
-- Check if stock properly decremented after order
SELECT p.id, p.jumlah_kg, SUM(dt.jumlah_kg) as ordered
FROM penawarans p
LEFT JOIN detail_transaksis dt ON p.id = dt.hasil_tangkapan_id
GROUP BY p.id;

-- Expected: jumlah_kg = original - sum(ordered)
```

### Verify No Overselling
```sql
-- Check for any oversold items
SELECT p.id, p.jumlah_kg, SUM(dt.jumlah_kg) as total_ordered
FROM penawarans p
JOIN detail_transaksis dt ON p.id = dt.hasil_tangkapan_id
GROUP BY p.id
HAVING total_ordered > p.jumlah_kg + SUM(dt.jumlah_kg);

-- Expected: 0 results
```

---

## 🔧 Browser Console Testing

### Test Image Loading
```javascript
// Check if images load correctly
document.querySelectorAll('img').forEach(img => {
  console.log(`Image: ${img.src} - Width: ${img.naturalWidth}`);
});
```

### Test Responsive Behavior
```javascript
// Check current viewport
console.log(`Viewport: ${window.innerWidth}x${window.innerHeight}`);

// Toggle device toolbar to test different sizes
// DevTools: Ctrl+Shift+M (or Cmd+Shift+M on Mac)
```

### Test Total Price Calculation
```javascript
// Simulate quantity change
document.getElementById('jumlah').value = 5;
document.getElementById('jumlah').dispatchEvent(new Event('input'));
console.log(document.getElementById('totalPrice').textContent);
```

---

## 🚀 Performance Testing

### Lighthouse Checklist
- [ ] Performance score > 80
- [ ] Accessibility score > 90
- [ ] Best Practices score > 90
- [ ] SEO score > 90

### Load Testing
```
1. [ ] Browse page loads < 2 seconds
2. [ ] Detail page loads < 1.5 seconds
3. [ ] Images lazy load if needed
4. [ ] No console errors
```

### Network Testing
```
1. [ ] CSS/JS files minified
2. [ ] Images optimized (< 500KB each)
3. [ ] No render-blocking resources
4. [ ] Compression enabled (gzip)
```

---

## 📝 Manual Testing Checklist

### Order Flow Testing (Complete Flow)
```
1. [ ] Login as pembeli
2. [ ] Navigate to browse
3. [ ] Apply filters (jenis ikan, nelayan)
4. [ ] View product details
5. [ ] Enter quantity
6. [ ] Submit order
7. [ ] Confirm success message
8. [ ] Verify order in dashboard
9. [ ] Check stock decreased
10. [ ] Verify total price calculated correctly
```

### Admin/Tengkulak Approval Flow
```
1. [ ] Login as tengkulak
2. [ ] View pending penawaran
3. [ ] Approve penawaran
4. [ ] Check hasil_tangkapan created with correct grade
5. [ ] Check foto_ikan has 'storage/' prefix
6. [ ] Verify pembeli can now see in browse
```

### Security Boundary Testing
```
1. [ ] Nelayan cannot approve penawaran
2. [ ] Pembeli cannot create penawaran
3. [ ] Sopir cannot approve transaksi
4. [ ] User cannot modify own grade/price
```

---

## 🐛 Common Issues & Solutions

### Issue: Image not displaying in tengkulak dashboard
```
Solution:
1. Verify storage:link created: ls -la public/storage
2. Check foto_ikan path in DB: SELECT foto_ikan FROM hasil_tangkapans LIMIT 1
3. Should start with 'storage/penawarans/...'
4. In view: src="{{ asset('storage/' . $item->foto_ikan) }}"
```

### Issue: Grade error when approving
```
Solution:
1. Check kualitas value: SELECT DISTINCT kualitas FROM penawarans
2. Verify mapping: 'premium'=>'A', 'standar'=>'B', 'ekonomi'=>'C'
3. Check grade enum: SHOW COLUMNS FROM hasil_tangkapans LIKE 'grade'
4. Should be ENUM('A','B','C')
```

### Issue: Form shows "Terlalu banyak permintaan" error
```
Solution:
1. This is rate limiting (1 submission per minute)
2. Wait 60 seconds before trying again
3. Check: created_at >= now()->subMinutes(1)
4. Change threshold in controller if needed
```

### Issue: Mobile view not responsive
```
Solution:
1. Check viewport meta tag in head: <meta name="viewport" ...>
2. Should be: content="width=device-width, initial-scale=1.0"
3. Clear browser cache (Ctrl+Shift+Delete)
4. Check Bootstrap CSS is loaded (DevTools > Network)
```

---

## 🎓 Code Review Checklist

- [ ] All user input validated with whitelist
- [ ] All numeric values type-casted
- [ ] All string output sanitized with htmlspecialchars
- [ ] All file uploads validated (MIME + size + dimensions)
- [ ] All sensitive operations have authorization checks
- [ ] All errors logged with context
- [ ] All database transactions have rollback on error
- [ ] All forms have CSRF token (Laravel default)
- [ ] All race conditions prevented with locking
- [ ] All error messages are generic (no stack traces)

---

## 📞 Support Contact

For issues or questions about security/responsive updates:
1. Check SECURITY_AND_RESPONSIVE_UPDATE.md (full documentation)
2. Check error logs in storage/logs/laravel.log
3. Review controller comments for implementation details
4. Test with provided test cases above

---

**Last Updated:** Latest Session  
**Framework Version:** Laravel 11  
**Bootstrap Version:** 5.x  
**Status:** 🟢 Ready for Testing
