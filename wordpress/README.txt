WORDPRESS + POLYLANG KURULUMU (LOCALWP)

Bu klasörde iki kurulabilir paket bulunur:
- portfoylo-theme: Responsive portfolyo teması ve yerel görseller
- portfoylo-content-importer: Türkçe/İngilizce sayfaları ve blog yazılarını oluşturan kurulum eklentisi

LOCALWP
1. LocalWP içinde Create a new site ile boş bir WordPress sitesi oluşturun.
2. Yönetici kullanıcı adı ve parolasını kendiniz belirleyin; teslim raporuna yazmak için güvenli bir yerde saklayın. Parolanızı GitHub'a veya sohbet mesajlarına koymayın.
3. WordPress yönetimine giriş yapın ve Polylang eklentisini kurup etkinleştirin.
4. Languages → Languages bölümünden Türkçe (tr) ve İngilizce (en) dillerini ekleyin. Türkçeyi varsayılan dil yapın.
5. Appearance → Themes → Add New → Upload Theme yolundan portfoylo-theme.zip dosyasını yükleyin ve etkinleştirin.
6. Plugins → Add New → Upload Plugin yolundan portfoylo-content-importer.zip dosyasını yükleyin ve etkinleştirin.
7. Tools → Portfolio Content Setup sayfasını açıp Install bilingual portfolio content düğmesine basın. Eklenti, sayfaları ve yazıları oluşturur; Türkçe/İngilizce eşleşmelerini, kategorileri ve etiketleri Polylang'a kaydeder.
8. Settings → Permalinks bölümünde yazı adı yapısını seçip kaydedin.
9. Ön yüzde altı sayfayı, dil değiştiriciyi ve her dildeki üç blog yazısını kontrol edin.

İÇERİK
- Ana sayfa, Hakkımda, Projeler, Galeri, İletişim ve Makaleler içerikleri WordPress sayfalarına aktarılır.
- Üç makale her dilde ayrı WordPress yazısıdır; kategoriler ve etiketler ayrıca atanır.
- WordPress iletişim formu, REST API üzerinden doğrulanmış mesajı goksuduzgun@gmail.com adresine wp_mail() ile gönderir. Canlı hostingde teslimat için SMTP ayarlarının yapılması gerekir. Statik HTML sürümü ziyaretçinin e-posta uygulamasını açar.
- Görseller ve ana sayfanın sessiz, döngüsel arka plan videosu tema paketine yerel olarak eklenmiştir. Harita Google Maps yerleştirmesi olarak internet bağlantısı gerektirir.
- Blog yazılarının ve sayfaların HTML içeriği, içe aktarımdan sonra WordPress panelinden düzenlenebilir.
- Kurulum eklentisi yalnızca kendi oluşturduğu içerikleri günceller. Aynı URL kısa adına sahip, başka bir içerik varsa verinin üzerine yazmak yerine hata verir.

.WPRESS YEDEĞİ
All-in-One WP Migration eklentisini kurup etkinleştirin. All-in-One WP Migration → Export → File ile siteyi dışa aktarın. Teslim yönergesindeki 500 MB sınırını aşmaması için medya dosyalarını optimize edin. Üretilen .wpress dosyasını ve yönetici giriş bilgilerini yalnızca öğretmenin istediği güvenli teslim alanında paylaşın; parolayı depoya eklemeyin.

YAYINLAMA
LocalWP yalnızca yerel geliştirme ortamıdır. Alan adı/hosting ve site yayını bu paket tarafından otomatik olarak sağlanmaz. Yayına alma için ayrıca bir hosting hesabı ve alan adı gerekir.
