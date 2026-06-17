🇹🇷 **Türkçe** | 🇬🇧 [English](README.md)

---

# DayPick – Contact Form 7 için Tarih & Saat Seçici

Contact Form 7'ye modern, mobil uyumlu bir tarih ve saat seçici ekleyen hafif bir eklenti. [flatpickr](https://flatpickr.js.org/) üzerine kuruludur; lisans anahtarı, pro sürüm veya ücretli çeviri yoktur.

## 🚀 Özellikler

- **Yerel görünüm, ISO kayıt:** Ziyaretçi tarihi kendi formatında görür (ör. `15.07.2026`), ancak form her zaman temiz bir ISO değeri gönderir (`2026-07-15`). AM/PM karışıklığı ve bozuk veritabanı dışa aktarımları sona erer.
- **Sunucu tarafı doğrulama:** min/max tarihleri, kapalı günler ve saat aralıkları yalnızca tarayıcıda değil, sunucuda da zorlanır — kurallar atlatılamaz.
- **Otomatik dil:** Seçici, WordPress site dilinizi kutudan çıktığı gibi takip eder. flatpickr'ın 50+ dili eklentiyle birlikte gelir.
- **Görsel etiket oluşturucu:** Contact Form 7 editöründen, tek satır kod yazmadan alan ekleyin.
- **Yalnızca gerektiğinde yüklenir:** Asset'ler yalnızca bir DayPick alanı render eden sayfalarda, `defer` stratejisiyle kuyruğa alınır.
- **Tema güvenli stil:** İzole edilmiş popup CSS'i tema çakışmalarını önler.

## ⚙️ Kurulum

1. Contact Form 7'yi kurun ve etkinleştirin.
2. **Eklentiler → Yeni Ekle** bölümünden "DayPick" aratıp kurun ve etkinleştirin (ya da bu depoyu `wp-content/plugins/` altına kopyalayın).
3. Bir formu düzenleyin ve **tarih/saat seçici (DayPick)** düğmesini kullanın veya elle `[daypick alan-adi]` etiketi ekleyin.

## 📝 Kullanım

Formunuza, editördeki **DayPick** düğmesiyle veya elle bir etiket ekleyin:

```
[daypick* randevu mode:datetime min:today max:+90d hours:09:00-18:00 step:30 disable:weekends firstday:1]
```

### Kullanılabilir seçenekler

| Seçenek | Açıklama |
| --- | --- |
| `mode:date` / `mode:time` / `mode:datetime` | Seçici türü (varsayılan: `date`) |
| `min:today`, `min:2026-07-01`, `min:+7d` | Seçilebilir en erken tarih |
| `max:+90d`, `max:2026-09-30` | Seçilebilir en geç tarih |
| `hours:09:00-18:00` | İzin verilen saat aralığı |
| `step:30` | Dakika adımı (1–60) |
| `disable:weekends`, `disable:2026-07-15,2026-07-16` | Kapalı günler (birlikte kullanılabilir) |
| `firstday:1` | Haftanın ilk günü (0 = Pazar, 1 = Pazartesi; varsayılan: site ayarı) |
| `format:d.m.Y` | Görünen format (PHP date token'ları; boşluk için `_` kullanın, ör. `format:d.m.Y_H:i`) |
| `"Bir tarih seçin" placeholder` | Tırnaklı metni alan placeholder'ı yapar; `placeholder` olmadan aynı metin ön-dolu varsayılan değer olur |

Gönderilen değer moda göre her zaman ISO'dur: `Y-m-d`, `H:i` veya `Y-m-d H:i`. E-posta şablonunuzda normal mail-tag'i (ör. `[randevu]`) kullanın.

## ❓ Sık Sorulan Sorular

**Lisans anahtarı veya pro sürüm gerekir mi?**
Hayır. Tüm özellikler ve tüm çeviriler ücretsizdir.

**E-postada hangi değer gönderilir?**
Varsayılan olarak ISO değeri (ör. `2026-07-15 14:30`). Etikete bir görünen format verirseniz (ör. `format:d.m.Y_H:i`), e-posta da aynı formatı kullanır (ör. `15.07.2026 14:30`), site diliniz ve saat diliminize göre yerelleştirilir. Saklanan/dışa aktarılan veri (veritabanı, CFDB7) tutarlılık için her zaman ISO kalır.

**Ziyaretçiler tarih kısıtlamalarını atlayabilir mi?**
Hayır. min/max, kapalı günler, saat aralığı ve dakika adımı gönderim sırasında sunucuda doğrulanır.

**Mobilde çalışır mı?**
Evet. DayPick mobil cihazlarda da aynı seçiciyi render eder; böylece kurallarınız (kapalı günler, saat aralıkları) her yerde geçerli kalır.

## 🔌 Krediler

DayPick, MIT lisanslı (GPL uyumlu) [flatpickr](https://flatpickr.js.org/) kütüphanesini (v4.6.13) içerir. Sıkıştırılmamış kaynak kodu [flatpickr GitHub deposunda](https://github.com/flatpickr/flatpickr/tree/v4.6.13) bulunabilir.

## 📄 Lisans

GPLv2 veya üstü — [GNU GPL v2](https://www.gnu.org/licenses/gpl-2.0.html).

## 🤝 Katkıda Bulunma

Pull request'ler memnuniyetle karşılanır. Büyük değişiklikler için, lütfen önce neyi değiştirmek istediğinizi tartışmak üzere bir issue açın!

---

[Özlem Çimen](https://www.linkedin.com/in/ozlemcimen/) tarafından geliştirildi — Kurumsal WordPress danışmanlığı: [Wolinka](https://wolinka.com)
