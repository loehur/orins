<main class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-qrcode me-2"></i>QR Code Generator</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="qrText" class="form-label">Masukkan Teks atau URL</label>
                        <textarea class="form-control" id="qrText" rows="3" placeholder="Ketik teks atau URL yang ingin dijadikan QR Code..."></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary bg-gradient" id="generateBtn">
                            <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Generate QR Code
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mt-3" id="qrResult" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fa-solid fa-image me-2"></i>Hasil QR Code</h6>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="d-inline-block p-3 bg-white border rounded"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success bg-gradient" id="downloadBtn">
                            <i class="fa-solid fa-download me-2"></i>Download QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
    var qrcode = null;
    
    $('#generateBtn').click(function() {
        var text = $('#qrText').val().trim();
        
        if (text === '') {
            alert('Masukkan teks atau URL terlebih dahulu!');
            return;
        }
        
        // Clear previous QR code
        $('#qrcode').empty();
        
        // Generate new QR code
        qrcode = new QRCode(document.getElementById("qrcode"), {
            text: text,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        // Show result card
        $('#qrResult').fadeIn();
    });
    
    $('#downloadBtn').click(function() {
        var canvas = $('#qrcode canvas')[0];
        if (canvas) {
            var link = document.createElement('a');
            link.download = 'qrcode.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        } else {
            // Fallback for img element
            var img = $('#qrcode img')[0];
            if (img) {
                var link = document.createElement('a');
                link.download = 'qrcode.png';
                link.href = img.src;
                link.click();
            }
        }
    });
    
    // Generate on Enter key
    $('#qrText').keypress(function(e) {
        if (e.which == 13 && !e.shiftKey) {
            e.preventDefault();
            $('#generateBtn').click();
        }
    });
</script>
