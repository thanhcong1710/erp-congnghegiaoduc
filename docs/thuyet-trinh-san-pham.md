# ERP GIAO DUC — THUYET TRINH THEO HANH TRINH KHACH HANG

## LUONG TONG QUAN
DATA THO -> KHACH HANG -> CHAM SOC -> THEM HOC SINH (CHINH THUC) -> NHAP HOC (1-CHAM) -> XEP LOP -> DIEM DANH -> VAN HANH

---

## BUOC 1: NHAP DATA THO (CRM)

### Kenh dua data vao he thong

**1.1 Import hang loat tu Excel**
- Upload file Excel danh sach khach hang tiem nang tu cac kenh (Facebook Ads, Zalo, Landing Page...)
- He thong validate du lieu va gan nguon (Source) ngay khi import
- Ho tro import so luong lon, tiet kiem thoi gian nhap lieu

**1.2 Them moi thu cong**
- Form tao khach hang voi day du truong: Ho ten, SDT, Email, Nguon, Nguon chi tiet, Nguoi phu trach, Ghi chu
- Tich hop validation SDT trung ngay khi nhap — tranh data duplicate

> Demo diem: Nhan "Them moi" -> Dien thong tin -> He thong bao ngay neu SDT da ton tai

---

## BUOC 2: QUAN LY DANH SACH KHACH HANG (CRM)

### Danh sach khach hang thong minh

Man hinh danh sach phan tab tu dong theo muc do uu tien xu ly:

| Tab | Y nghia |
|-----|---------|
| Tat ca | Toan bo khach hang trong pham vi phu trach |
| Chua cham soc | Khach hang moi, chua co lan lien he nao |
| Lich cham soc trong ngay | Khach hang den han can goi hom nay |
| KH qua han xu ly | Da qua ngay hen, chua duoc xu ly |
| Ticket chua xu ly | Yeu cau/khieu nai dang cho |

**Bo loc nhanh:**
- Theo tu khoa (ten, SDT)
- Theo trang thai khach hang
- Theo nguoi phu trach (EC)
- Theo nguon (Facebook, Zalo, Referral...)
- Theo lich cham soc tiep theo

**Tinh nang Ban giao hang loat:**
- Chon nhieu khach hang cung luc -> Ban giao sang EC khac chi voi 2 click

---

## BUOC 3: CHAM SOC KHACH HANG (CRM)

### Ho so chi tiet khach hang

Moi khach hang co trang ho so day du:

- Thong tin ca nhan — Ho ten, SDT, Email, Dia chi, Link Facebook, Nguon
- Lich su cham soc — Timeline toan bo cac lan tuong tac: goi dien, gap truc tiep, nhan tin...
- Lan cham soc gan nhat — Noi dung cuoc tro chuyen
- Lich cham soc tiep theo — Nhan vien dat lich hen, he thong nhac nho
- Trang thai — Theo doi tien trinh chuyen doi (Moi -> Dang quan tam -> Dong y mua)
- Nguoi phu trach — Gan EC chiu trach nhiem

**Quy trinh cham soc:**
Xem khach hang -> Ghi nhat ky cham soc -> Dat lich hen tiep theo
-> Cap nhat trang thai -> Bao cao chuyen doi

---

## BUOC 4: DONG Y MUA — THEM HOC SINH & NHAP HOC (LMS)

### DIEM CAI TIEN: BO BUOC CHECKIN TRUNG GIAN — LEN CHINH THUC & NHAP HOC NGAY
He thong da rut gon toan bo quy trinh phuc tap cu (Them HS -> Dat lich checkin -> Den co so checkin -> Bam nang cap len chinh thuc):
- **Them moi hoc sinh = Hoc sinh chinh thuc ngay lap tuc**: Ngay khi them hoc sinh tai ho so Khach hang CRM hoac danh sach KH, he thong tu dong:
  - Sinh ma hoc sinh LMS chuan (`STK...`)
  - Ghi nhan vao bang hoc sinh LMS (`students` va `term_student_user`)
  - Gan trung tam va EC phu trach
  - Trang thai tu dong la **Chinh thuc**
- **Nhap hoc 1-cham (Quick Enroll)**:
  - Tren the hoc sinh tai CRM co nut **"Nhap hoc"**: Click se mo ngay man hinh Tao hop dong nhap hoc (`/lms/contracts/add`) voi Hoc sinh va Co so da duoc dien san.
  - Tai ho so chi tiet hoc sinh LMS (`/lms/students/:id/detail`), cung co san nut **"Nhap hoc ngay"**.
  - EC chi can chon goi hoc phi, len phieu thu va bam Luu -> Hop dong duoc tao tuc thi.

### Hai luong nhap hoc linh hoat

**Luong A — Nhap hoc 1-cham tu CRM hoac Ho so hoc sinh (Khuyen nghi)**
- Tu the hoc sinh o tab "Hoc sinh" (CRM) hoac trang ho so hoc sinh (LMS) -> Nhan nut **"Nhap hoc"**
- He thong tu dong mo form Tao hop dong, dien san thong tin hoc sinh, co so, EC phu trach
- Chon goi hoc phi (loc theo co so) -> xem ngay chi tiet buoi, don gia, uu dai
- Chon lop hoc neu xep lop luon (hoac de xep lop sau)
- Luu -> Tao hop dong + cap nhat cong no/phieu thu ngay

**Luong B — Nhap hoc tu menu Quan ly Hop dong (`/lms/contracts/add`)**
- Chon trung tam -> Tim kiem hoc sinh theo Ten, So dien thoai hoac Ma LMS
- He thong hien thi ngay thong tin hoc sinh (so buoi con lai, lich su dang ky)
- Chon goi hoc phi & lop hoc -> Luu hop dong

---

## BUOC 5: XEP LOP (LMS)

### Thong tin lop hoc tich hop ngay tai form dang ky

Khi chon lop, he thong hien thi ngay lap tuc panel thong tin:

| Thong tin | Y nghia |
|-----------|---------|
| Giao vien / TA | Ai dung lop |
| CM | Class Manager phu trach |
| Lich hoc | Thu may, ca nao |
| Phong hoc | Phong vat ly hoac Zoom Room |
| Khoa hoc | IELTS / TOEIC / Giao tiep... |
| Ngay bat dau — Ket thuc | Khung thoi gian |
| Si so | X/Max — hien thi mau canh bao neu gan day |
| Trang thai | Sap khai giang / Dang dien ra |

**Danh sach lop duoc loc thong minh:**
- Chi hien lop co ngay bat dau >= hom nay
- Loc theo goi hoc phi da chon
- Loc theo trung tam

**Quan ly lop hoc (man hinh rieng):**
- Xem tat ca lop, loc theo trang thai si so (Thieu / Du / Thua)
- Gan / thay doi Giao vien, TA, CM
- Cau hinh lich hoc — thu, phong, ca

---

## BUOC 6: DIEM DANH (LMS)

- Diem danh tung hoc vien theo buoi hoc
- Check-in tai trung tam (co the dung khi khach den)
- He thong cap nhat tu dong so buoi da hoc / con lai tren tung hop dong
- Xem lich su diem danh cua hoc vien bat ky luc nao

---

## BUOC 7: VAN HANH — CAC NGHIEP VU DAC BIET (LMS)

### 7.1 Chuyen lop (Class Transfer)
- Hoc vien muon doi sang lop khac (dung khoa hoc)
- He thong kiem tra dieu kien va giu nguyen so buoi con lai

### 7.2 Chuyen ky / Chuyen lien khoa (Semester Transfer - TINH NANG MOI)
- Chuyen hoc vien tu lop khoa hoc NAY sang lop khoa hoc KHAC
- Vi du: Hoc xong IELTS Foundation -> chuyen sang IELTS Intermediate
- Giao dien cai tien: 2 dropdown doc lap — Khoa chuyen / Khoa nhan
- Danh sach lop tu nap rieng cho tung ben

### 7.3 Chuyen trung tam (Branch Transfer)
- Di chuyen hoc vien sang chi nhanh khac
- Toan bo lich su hoc tap duoc giu nguyen

### 7.4 Bao luu (Reserve)
- Hoc vien tam dung hoc vi ly do ca nhan
- Xac dinh ngay bao luu va ngay du kien quay lai
- So buoi hoc con lai duoc bao toan 100%

### 7.5 Doi lich (Exchange)
- Doi 1 buoi cu the sang ngay/ca khac
- Khong anh huong den lich hoc tong the

### 7.6 Chuyen hoc phi (Tuition Transfer)
- Chuyen so du hoc phi tu goi nay sang goi khac
- Tu dong tinh doi soat: tien nao duoc chuyen tiep, can dong them bao nhieu

---

## AI ASSISTANT — DIEM NHAN KHAC BIET

> Tich hop Google Gemini AI — Hoi bang tieng Viet tu nhien, nhan ket qua tu du lieu that

### Trong van hanh hang ngay, AI ho tro:

EC hoi: "Hoc vien Nguyen Thi Mai SDT 0901... dang hoc lop nao, con bao nhieu buoi?"
AI tra loi: Tra du lieu that -> Tra ve thong tin ngay lap tuc

Manager hoi: "Doanh so tuan nay cua CS Ha Noi so voi tuan truoc?"
AI tra loi: Tong hop va so sanh so lieu real-time

Nhan vien hoi: "Lop nao dang thieu hoc sinh, can tuyen them?"
AI tra loi: Danh sach lop co si so duoi nguong

Khong can nho duong dan menu — Hoi nhu chat, nhan ket qua ngay.

---

## TONG KET LUONG

DATA THO (Import / Them moi)
        |
DANH SACH KH (tab phan loai tu dong)
        |
CHAM SOC -> Ghi lich su -> Dat lich hen -> Cap nhat trang thai
        |
THEM HOC SINH -> Tu dong len Chinh thuc + Cap ma LMS tuc thi (Bo check-in)
        |
DONG Y MUA -> Nhap hoc 1-cham (Dien san HS, chon goi hoc phi, len phieu thu)
        |
XEP LOP -> Xem chi tiet lop ngay tai form
        |
DIEM DANH -> Cap nhat so buoi tu dong
        |
VAN HANH: Bao luu / Doi lich / Chuyen lop / Chuyen ky / Chuyen TT
        |
AI HO TRO tra cuu & bao cao bang tieng Viet moi luc

---

Cap nhat: 2026-11-11 | Phien ban: 2.0 (Toi uu hoa quy trinh Nhap hoc & Bo Checkin trung gian)
