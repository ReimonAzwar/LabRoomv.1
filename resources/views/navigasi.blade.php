<style>
  .canva-frame-container {
    position: relative;
    width: 100%;
    height: 0;
    /* Rasio untuk Desktop (Sesuai kode asli Anda) */
    padding-top: 292.02%; 
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    margin: 1.5em 0;
  }

  /* Penyesuaian Otomatis untuk Mobile (Layar di bawah 768px) */
  @media (max-width: 767px) {
    .canva-frame-container {
      /* Menambah tinggi di HP agar konten tidak terlihat mengecil */
      padding-top: 350%; 
    }
  }

  .canva-embed-item {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    border: none;
  }

  /* Tombol Peminjaman Lab */
  .btn-peminjaman {
    display: inline-block;
    margin: 10px;
    padding: 10px 20px;
    background-color: #0D2244;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-family: sans-serif;
    font-weight: bold;
  }
</style>

<a href="/" class="btn-peminjaman">Peminjaman Lab</a>

<div class="canva-frame-container">
  <iframe 
    class="canva-embed-item"
    loading="lazy" 
    src="https://www.canva.com/design/DAHJHEUu0Zo/v00qB_L145CDv3WTsDB8kg/view?embed" 
    allowfullscreen>
  </iframe>
</div>
