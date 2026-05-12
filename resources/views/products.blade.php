@extends('layout')

@section('style')
    <style>
        #product-barcode-reader{
            width: 100%;
            max-width: 420px;
        }

        #product-barcode-reader video{
            border-radius: 6px;
        }
    </style>
@endsection

@section('modal')
    <div class="modal fade" id="add-product" tabindex="-1" aria-labelledby="add-productLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add-productLabel">Add Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('products.store') }}" method="post">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="border rounded p-2 mb-2">
                            <label for="product-barcode" class="text-muted">Barcode :</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="text" id="product-barcode" name="barcode" class="form-control py-1" style="width: 180px" autocomplete="off" value="{{ old('barcode') }}">
                                <button type="button" class="btn btn-outline-primary py-1" id="start-product-scanner">Scan</button>
                                <button type="button" class="btn btn-outline-secondary py-1 d-none" id="stop-product-scanner">Stop</button>
                                <label class="btn btn-outline-dark py-1 m-0" for="product-barcode-file">Image</label>
                                <input type="file" id="product-barcode-file" class="d-none" accept="image/*">
                            </div>
                            <div id="product-barcode-reader" class="mt-2 d-none"></div>
                            <small id="product-scan-status" class="text-muted d-block mt-1">Scan or type the product barcode.</small>
                        </div>

                        <div class="d-flex flex-column mb-2">
                            <label for="product-name" class="text-muted">Name :</label>
                            <input required type="text" id="product-name" name="product_name" class="form-control py-1" value="{{ old('product_name') }}">
                        </div>
                        <div class="d-flex flex-column mb-2">
                            <label for="product-price" class="text-muted">Price :</label>
                            <input required type="number" id="product-price" name="price" class="form-control py-1" min="0" step="0.01" value="{{ old('price') }}">
                        </div>
                        <div class="d-flex flex-column">
                            <label for="product-stock" class="text-muted">Stock Quantity :</label>
                            <input required type="number" id="product-stock" name="stock_quantity" class="form-control py-1" min="0" value="{{ old('stock_quantity', 0) }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
        <h1 class="fs-4 m-0">Products</h1>
        <div>
            <button class="btn btn-success py-1" data-bs-toggle="modal" data-bs-target="#add-product">Add Product</button>
            <a href="{{ route('home') }}" class="btn btn-secondary py-1">Back</a>
        </div>
    </div>

    <div class="table-group table-responsive border border-dark" style="max-height: 85vh">
        <table class="table table-hover mb-0">
            <thead class="sticky-top z-1">
                <tr>
                    <th>Barcode</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->barcode ?? '--' }}</td>
                        <td>{{ $product->name }}</td>
                        <td>
                            {{ number_format($product->price, 2) }}
                        </td>
                        <td>{{ $product->stock_quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('js')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    const productBarcodeInput = document.getElementById('product-barcode')
    const productScanStatus = document.getElementById('product-scan-status')
    const startProductScannerBtn = document.getElementById('start-product-scanner')
    const stopProductScannerBtn = document.getElementById('stop-product-scanner')
    const productBarcodeFileInput = document.getElementById('product-barcode-file')
    const productScannerReader = document.getElementById('product-barcode-reader')
    const addProductModal = document.getElementById('add-product')
    let productScanner = null
    let productScannerIsRunning = false
    let productScanTimer = null
    let lastProductScanText = ''

    function setProductScanStatus(message, type = 'muted'){
        productScanStatus.textContent = message
        productScanStatus.classList.remove('text-muted', 'text-success', 'text-danger')
        productScanStatus.classList.add(`text-${type}`)
    }

    function fillProductBarcode(barcode){
        const cleanBarcode = barcode.trim()
        productBarcodeInput.value = cleanBarcode
        setProductScanStatus(`Barcode ${cleanBarcode} scanned.`, 'success')
    }

    function createProductScanner(){
        if(productScanner){
            return productScanner
        }

        const formats = typeof Html5QrcodeSupportedFormats !== 'undefined'
            ? [
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.CODABAR,
                Html5QrcodeSupportedFormats.ITF,
                Html5QrcodeSupportedFormats.QR_CODE,
            ]
            : undefined

        productScanner = new Html5Qrcode('product-barcode-reader', {
            formatsToSupport: formats,
            useBarCodeDetectorIfSupported: true
        })

        return productScanner
    }

    function getBackCameraId(cameras){
        const backCamera = cameras.find(camera => {
            const label = camera.label.toLowerCase()
            return label.includes('back') || label.includes('rear') || label.includes('environment')
        })

        return (backCamera || cameras[0]).id
    }

    function productCameraErrorMessage(err){
        const message = err && (err.message || err.toString()) ? (err.message || err.toString()) : 'Unknown camera error.'

        if(!window.isSecureContext){
            return 'Camera is blocked because this page is not using HTTPS or localhost.'
        }

        if(message.includes('NotAllowedError') || message.includes('Permission')){
            return 'Camera permission was blocked. Allow camera access, then try again.'
        }

        if(message.includes('NotFoundError') || message.includes('No camera')){
            return 'No camera was found on this device/browser.'
        }

        return `Camera/scanner error: ${message}`
    }

    function stopProductScanner(){
        clearInterval(productScanTimer)

        if(!productScanner || !productScannerIsRunning){
            productScannerReader.classList.add('d-none')
            startProductScannerBtn.classList.remove('d-none')
            stopProductScannerBtn.classList.add('d-none')
            return Promise.resolve()
        }

        return productScanner.stop()
            .then(() => {
                productScannerIsRunning = false
                productScannerReader.classList.add('d-none')
                startProductScannerBtn.classList.remove('d-none')
                stopProductScannerBtn.classList.add('d-none')
            })
            .catch(err => {
                console.error(err)
            })
    }

    function startProductScanner(cameraConfig){
        return createProductScanner().start(
            cameraConfig,
            {
                fps: 15,
                aspectRatio: 1.777778,
                disableFlip: false,
                videoConstraints: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            },
            decodedText => {
                if(decodedText === lastProductScanText){
                    return
                }

                lastProductScanText = decodedText
                fillProductBarcode(decodedText)
                stopProductScanner()
            },
            (errorMessage, error) => {
                if(error && error.type !== 2){
                    console.debug(errorMessage, error)
                }
            }
        )
    }

    startProductScannerBtn.addEventListener('click', function(){
        if(typeof Html5Qrcode === 'undefined'){
            setProductScanStatus('Barcode scanner failed to load.', 'danger')
            return
        }

        productScannerReader.classList.remove('d-none')
        startProductScannerBtn.classList.add('d-none')
        stopProductScannerBtn.classList.remove('d-none')
        setProductScanStatus('Starting camera...')

        Html5Qrcode.getCameras()
            .then(cameras => {
                if(cameras.length){
                    return startProductScanner(getBackCameraId(cameras))
                }

                return startProductScanner({
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                })
            })
            .then(() => {
                let seconds = 0
                productScannerIsRunning = true
                setProductScanStatus('Camera started. Move closer until the barcode lines are sharp.')
                clearInterval(productScanTimer)
                productScanTimer = setInterval(() => {
                    seconds += 2
                    setProductScanStatus(`Scanning... ${seconds}s. Keep the barcode flat, bright, and sharp.`)
                }, 2000)
            })
            .catch(err => {
                console.error(err)
                productScannerIsRunning = false
                productScannerReader.classList.add('d-none')
                startProductScannerBtn.classList.remove('d-none')
                stopProductScannerBtn.classList.add('d-none')
                setProductScanStatus(productCameraErrorMessage(err), 'danger')
            })
    })

    stopProductScannerBtn.addEventListener('click', stopProductScanner)
    addProductModal.addEventListener('hidden.bs.modal', stopProductScanner)
    productBarcodeFileInput.addEventListener('change', function(){
        if(!this.files.length){
            return
        }

        if(typeof Html5Qrcode === 'undefined'){
            setProductScanStatus('Barcode scanner failed to load.', 'danger')
            return
        }

        createProductScanner().scanFile(this.files[0], true)
            .then(decodedText => {
                fillProductBarcode(decodedText)
            })
            .catch(err => {
                console.error(err)
                setProductScanStatus('Could not read a barcode from that image. Try a brighter, sharper photo.', 'danger')
            })
            .finally(() => {
                this.value = ''
            })
    })
</script>
@endsection
