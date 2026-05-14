
@extends('layout')
@section('style')
    <style>
        .inputs{
            width: min(90%, 250px)
        }

        #utang-barcode-reader,
        #pos-barcode-reader{
            width: 100%;
            max-width: 360px;
        }

        #utang-barcode-reader video,
        #pos-barcode-reader video{
            border-radius: 6px;
        }
    </style>
@endsection
@section('modal')
{{-- ADD PERSON MODAL --}}
    <div class="modal fade" id="add-person" tabindex="-1" aria-labelledby="add-personLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add-personLabel">Add Person</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add-person" method="post" id="addperson-form">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="">
                            <div class="d-flex flex-column">
                                <label for="" class="">Name :</label>
                                <input required type="text" name="person_name" class="form-control inputs py-1 border border-2 border-success">
                            </div>
                            <div class="d-flex flex-column mt-1">
                                <label for="" class="">Address :</label>
                                <div class="d-flex">
                                    <input required type="text" name="person_address" class="form-control inputs py-1 border border-2 border-primary" id="address">
                                    <div class="d-flex align-items-center ps-2">
                                        <input type="checkbox" name="" id="local" style="accent-color: green" onclick="localAdd(this)"> 
                                        <label for="local">Local</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- VIEW UTANG MODAL --}}
    <div class="modal fade" id="view-utang" tabindex="-1" aria-labelledby="view-utangLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="view-utangLabel">View Utang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 id="view-utang-person-name">Name</h3>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="show-paid-utang">
                                <label class="form-check-label" for="show-paid-utang">Show paid utang</label>
                            </div>
                        </div>
                    </div>
                    <div class="table-group table-responsive" style="max-height: 60vh">
                        <table class="table table-hover">
                            <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="view-utang-table">
                                    <tr>
                                        <td class="text-center" colspan="6">
                                            Select a person to view utang.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD UTANG MODAL --}}
    <div class="modal fade" id="add-utang" tabindex="-1" aria-labelledby="add-utangLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add-utangLabel">Add Utang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('add.utang') }}" method="post" id="addutang-form">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="person_id" id="utang-person-id">
                    <div class="modal-body">
                        <div class="d-flex">
                            <h3 id="utang-person-name">Name</h3>
                        </div>
                        <div class="px-3">
                            <div class="border rounded p-2 mb-2">
                                <label for="utang-barcode" class="text-muted">Barcode :</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <input type="text" id="utang-barcode" name="barcode" class="form-control py-1" style="width: 180px" autocomplete="off">
                                    <button type="button" class="btn btn-outline-primary py-1" id="start-utang-scanner">Scan</button>
                                    <button type="button" class="btn btn-outline-secondary py-1 d-none" id="stop-utang-scanner">Stop</button>
                                    <label class="btn btn-outline-dark py-1 m-0" for="utang-barcode-file">Image</label>
                                    <input type="file" id="utang-barcode-file" class="d-none" accept="image/*">
                                </div>
                                <div id="utang-barcode-reader" class="mt-2 d-none"></div>
                                <small id="utang-scan-status" class="text-muted d-block mt-1">Scan a product barcode to autofill item and price.</small>
                            </div>
                            <div class="d-flex flex-column">
                                <label for="utang-item" class="text-muted fs-5">Item :</label>
                                <input type="text" id="utang-item" name="item" class="ms-2 form-control py-1" style="width: 150px">
                            </div>
                            <div class="d-flex flex-column">
                                <label for="utang-qty" class="text-muted fs-5">Quantity :</label>
                                <input type="number" id="utang-qty" name="qty" class="ms-2 form-control py-1" style="width: 80px" min="1" value="1">
                            </div>
                            <div class="d-flex flex-column">
                                <label for="utang-price" class="text-muted fs-5">Price :</label>
                                <input type="number" id="utang-price" name="price" class="ms-2 form-control py-1" style="width: 100px" min="0" step="0.01">
                            </div>
                            <div class="">
                                <h4 class="text-center m-0" id="utang-total">P 0.00</h4>
                                <header for="" class="text-muted text-center fs-5 m-0">Total</header>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="">
        <div class="mt-2">
            <div class="d-flex justify-content-between p-2">
                <h6>User : {{ Auth::user()->name }}</h6>
                <div class="">
                    <button class="btn btn-secondary py-1" onclick="logout()">Logout</button>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <h5 class="text-muted">Dept Management | POS</h5>
            </div>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">   
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="lists-tab" data-bs-toggle="pill" data-bs-target="#lists" type="button" role="tab" aria-controls="lists" aria-selected="false">Lists</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pos-tab" data-bs-toggle="pill" data-bs-target="#pos" type="button" role="tab" aria-controls="pos" aria-selected="false">POS</button>
                </li>
            </ul>
            <div class="mt-2 d-flex">
                <button class="btn btn-success py-1 ms-2" data-bs-toggle="modal" data-bs-target="#add-person">Add Person</button>
                <a href="{{ route('products.index') }}" class="btn btn-primary py-1 ms-2">View Products</a>
                <a href="{{ route('sales.index') }}" class="btn btn-warning py-1 ms-2">Show Sales</a>
            </div>
        </div>
        

        <section class="py-2">
            <div class="border border-success rounded-3" style="height: 85vh">
                <div class="tab-content" id="pills-tabContent">
                    {{-- LISTS --}}
                    <div class="tab-pane fade show active p-2" id="lists" role="tabpanel" aria-labelledby="lists-tab" tabindex="0">
                        <div class="mb-2">
                            <input type="search" id="person-search" class="form-control py-1" placeholder="Search name..." style="max-width: 260px">
                        </div>
                        <div class="table-group table-responsive" style="height: 83vh">
                            <table class="table table-hover">
                                <thead class="sticky-top z-1">
                                    <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Total</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="persons-table">
                                    <tr>
                                        <td class="text-center" colspan="4">
                                            <button class="btn btn-primary" type="button" disabled>
                                                <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                                <span role="status">Loading...</span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="pos" role="tabpanel" aria-labelledby="pos-tab" tabindex="0">
                        <form action="{{ route('sales.store') }}" method="post" id="pos-form" style="max-width: 460px">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="product_id" id="pos-product-id">

                            <div class="border rounded p-2 mb-2">
                                <label for="pos-barcode" class="text-muted">Barcode :</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <input type="text" id="pos-barcode" name="barcode" class="form-control py-1" style="width: 180px" autocomplete="off">
                                    <button type="button" class="btn btn-outline-primary py-1" id="start-pos-scanner">Scan</button>
                                    <button type="button" class="btn btn-outline-secondary py-1 d-none" id="stop-pos-scanner">Stop</button>
                                    <label class="btn btn-outline-dark py-1 m-0" for="pos-barcode-file">Image</label>
                                    <input type="file" id="pos-barcode-file" class="d-none" accept="image/*">
                                </div>
                                <div id="pos-barcode-reader" class="mt-2 d-none"></div>
                                <small id="pos-scan-status" class="text-muted d-block mt-1">Scan or type a product barcode.</small>
                            </div>

                            <div class="d-flex flex-column mb-2">
                                <label for="pos-item" class="text-muted">Item :</label>
                                <input required type="text" id="pos-item" name="item" class="form-control py-1" readonly>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="d-flex flex-column mb-2">
                                    <label for="pos-qty" class="text-muted">Quantity :</label>
                                    <input required type="number" id="pos-qty" name="qty" class="form-control py-1" min="1" value="1" style="width: 100px">
                                </div>
                                <div class="d-flex flex-column mb-2">
                                    <label for="pos-price" class="text-muted">Price :</label>
                                    <input required type="number" id="pos-price" name="price" class="form-control py-1" min="0" step="0.01" readonly style="width: 120px">
                                </div>
                                <div class="d-flex flex-column mb-2">
                                    <label class="text-muted">Stock :</label>
                                    <input type="text" id="pos-stock" class="form-control py-1" readonly style="width: 100px">
                                </div>
                            </div>
                            <div class="mb-3">
                                <h4 class="m-0" id="pos-total">P 0.00</h4>
                                <span class="text-muted">Total</span>
                            </div>
                            <button type="submit" class="btn btn-success" id="pos-save" disabled>Save Sale</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    const barcodeProducts = @json($barcodeProducts);

    // LOCAL CHECK BOX
    function localAdd(check){
        let address = document.getElementById('address')
        if(check.checked){
            address.value = "Local"
            address.disabled = true
        }else{
            address.value = ""
            address.disabled = false
        }
    }

    // ADD PERSON FORM SUBMIT
    document.getElementById('addperson-form').addEventListener('submit', function(e){
        e.preventDefault();
        document.getElementById('address').disabled = false
        this.submit();
    })

    const csrfToken = '{{ csrf_token() }}'
    const personsTble = document.getElementById('persons-table')
    const personSearch = document.getElementById('person-search')
    const viewUtangModal = document.getElementById('view-utang')
    const viewUtangPersonName = document.getElementById('view-utang-person-name')
    const viewUtangTable = document.getElementById('view-utang-table')
    const showPaidUtangCheckbox = document.getElementById('show-paid-utang')
    let personsData = []
    let currentUtangData = []
    let showPaidUtang = false

    function renderPersons(){
        const search = personSearch.value.trim().toLowerCase()
        const filteredPersons = personsData.filter(person => person.name.toLowerCase().includes(search))

        personsTble.innerHTML = "";

        if(!filteredPersons.length){
            personsTble.innerHTML = `
                <tr>
                    <td class="text-center text-muted" colspan="4">No matching names found.</td>
                </tr>
            `
            return
        }

        filteredPersons.forEach(d => {
            let money = parseFloat(d.total).toLocaleString('en-PH',{
                style: 'currency',
                currency: 'PHP'
            })
            personsTble.innerHTML += `
                <tr>
                    <td>${d.name}</td>
                    <td>${d.address}</td>
                    <td>${money}</td>
                    <td class="text-center">
                        <button class="btn btn-primary py-1" data-bs-toggle="modal" data-bs-target="#view-utang" data-person-id="${d.id}" data-person-name="${d.name}">View</button>
                        <button class="btn btn-success py-1" data-bs-toggle="modal" data-bs-target="#add-utang" data-person-id="${d.id}" data-person-name="${d.name}">Add</button>
                    </td>
                </tr>
            `;
        });
    }
    
    function loadPersons(){
    fetch('get-person')
    .then(res => res.json())
    .then(data =>{
        if(data){
            personsData = data
            renderPersons()
        }
    })
    .catch(err =>{
        console.error(err)
    })
    }

    loadPersons()
    personSearch.addEventListener('input', renderPersons)

    function renderUtangTable(){
        const filteredData = currentUtangData.filter(utang => showPaidUtang || utang.status !== 'paid')

        if(!filteredData.length){
            const message = currentUtangData.length
                ? 'No unpaid utang found. Enable "Show paid utang" to view paid items.'
                : 'No utang found.'

            viewUtangTable.innerHTML = `
                <tr>
                    <td class="text-center text-muted" colspan="6">${message}</td>
                </tr>
            `
            return
        }

        viewUtangTable.innerHTML = ''

        filteredData.forEach(utang => {
            const price = formatPeso(utang.price)
            const total = formatPeso(utang.total)
            const isPaid = utang.status === 'paid'

            viewUtangTable.innerHTML += `
                <tr>
                    <td>${utang.item}</td>
                    <td>${utang.qty}</td>
                    <td>${price}</td>
                    <td>${total}</td>
                    <td>
                        <span class="badge ${isPaid ? 'text-bg-secondary' : 'text-bg-warning'}">${utang.status}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-success py-1 pay-utang-btn" data-utang-id="${utang.id}" ${isPaid ? 'disabled' : ''}>Pay</button>
                    </td>
                </tr>
            `
        })
    }

    function renderUtangRows(data){
        currentUtangData = data || []
        renderUtangTable()
    }

    function loadPersonUtang(personId){
        viewUtangTable.innerHTML = `
            <tr>
                <td class="text-center" colspan="6">
                    <button class="btn btn-primary" type="button" disabled>
                        <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                        <span role="status">Loading...</span>
                    </button>
                </td>
            </tr>
        `

        fetch(`person-utang/${personId}`)
            .then(res => res.json())
            .then(renderUtangRows)
            .catch(err => {
                console.error(err)
                viewUtangTable.innerHTML = `
                    <tr>
                        <td class="text-center text-danger" colspan="6">Unable to load utang.</td>
                    </tr>
                `
            })
    }

    viewUtangModal.addEventListener('show.bs.modal', function(event){
        const button = event.relatedTarget

        if(!button){
            return
        }

        const personId = button.getAttribute('data-person-id')
        viewUtangPersonName.textContent = button.getAttribute('data-person-name')
        viewUtangModal.setAttribute('data-person-id', personId)
        showPaidUtang = false
        showPaidUtangCheckbox.checked = false
        loadPersonUtang(personId)
    })

    showPaidUtangCheckbox.addEventListener('change', function(){
        showPaidUtang = this.checked
        renderUtangTable()
    })

    viewUtangTable.addEventListener('click', function(event){
        const button = event.target.closest('.pay-utang-btn')

        if(!button){
            return
        }

        button.disabled = true
        button.textContent = 'Paying...'

        fetch(`pay-utang/${button.getAttribute('data-utang-id')}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if(!res.ok){
                throw new Error('Unable to pay utang.')
            }

            return res.json()
        })
        .then(() => {
            loadPersonUtang(viewUtangModal.getAttribute('data-person-id'))
            loadPersons()
        })
        .catch(err => {
            console.error(err)
            button.disabled = false
            button.textContent = 'Pay'
        })
    })

    const addUtangModal = document.getElementById('add-utang')
    const addUtangForm = document.getElementById('addutang-form')
    const utangPersonIdInput = document.getElementById('utang-person-id')
    const utangPersonName = document.getElementById('utang-person-name')
    const barcodeInput = document.getElementById('utang-barcode')
    const itemInput = document.getElementById('utang-item')
    const qtyInput = document.getElementById('utang-qty')
    const priceInput = document.getElementById('utang-price')
    const totalText = document.getElementById('utang-total')
    const scanStatus = document.getElementById('utang-scan-status')
    const startScannerBtn = document.getElementById('start-utang-scanner')
    const stopScannerBtn = document.getElementById('stop-utang-scanner')
    const barcodeFileInput = document.getElementById('utang-barcode-file')
    const scannerReader = document.getElementById('utang-barcode-reader')
    let utangScanner = null
    let scannerIsRunning = false
    let lastScanText = ''
    let scanTimer = null

    function formatPeso(value){
        return parseFloat(value || 0).toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP'
        })
    }

    function updateUtangTotal(){
        const qty = parseFloat(qtyInput.value) || 0
        const price = parseFloat(priceInput.value) || 0
        totalText.textContent = formatPeso(qty * price)
    }

    function fillUtangProduct(barcode){
        const cleanBarcode = barcode.trim()
        const product = barcodeProducts[cleanBarcode]

        barcodeInput.value = cleanBarcode

        if(!cleanBarcode){
            scanStatus.textContent = 'Scan or type a product barcode.'
            scanStatus.classList.remove('text-danger', 'text-success')
            scanStatus.classList.add('text-muted')
            return
        }

        if(product){
            itemInput.value = product.item
            priceInput.value = product.price
            qtyInput.value = qtyInput.value || 1
            scanStatus.textContent = `Found ${product.item}. Stock: ${product.stock_quantity}.`
            scanStatus.classList.remove('text-danger', 'text-muted')
            scanStatus.classList.add('text-success')
        }else{
            scanStatus.textContent = `Barcode ${cleanBarcode} scanned, but no product match was found.`
            scanStatus.classList.remove('text-success', 'text-muted')
            scanStatus.classList.add('text-danger')
        }

        updateUtangTotal()
    }

    function stopUtangScanner(){
        if(!utangScanner || !scannerIsRunning){
            clearInterval(scanTimer)
            scannerReader.classList.add('d-none')
            startScannerBtn.classList.remove('d-none')
            stopScannerBtn.classList.add('d-none')
            return Promise.resolve()
        }

        return utangScanner.stop()
            .then(() => {
                scannerIsRunning = false
                clearInterval(scanTimer)
                scannerReader.classList.add('d-none')
                startScannerBtn.classList.remove('d-none')
                stopScannerBtn.classList.add('d-none')
            })
            .catch(err => {
                console.error(err)
            })
    }

    function createUtangScanner(){
        if(utangScanner){
            return utangScanner
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

        utangScanner = new Html5Qrcode('utang-barcode-reader', {
            formatsToSupport: formats,
            useBarCodeDetectorIfSupported: true,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        })

        return utangScanner
    }

    function getBackCameraId(cameras){
        const backCamera = cameras.find(camera => {
            const label = camera.label.toLowerCase()
            return label.includes('back') || label.includes('rear') || label.includes('environment')
        })

        return (backCamera || cameras[0]).id
    }

    function cameraErrorMessage(err){
        const message = err && (err.message || err.toString()) ? (err.message || err.toString()) : 'Unknown camera error.'

        if(!window.isSecureContext){
            return 'Camera is blocked because this page is not using HTTPS or localhost. Open it from http://127.0.0.1:8000/home on this device.'
        }

        if(message.includes('NotAllowedError') || message.includes('Permission')){
            return 'Camera permission was blocked. Allow camera access in the browser, then try again.'
        }

        if(message.includes('NotFoundError') || message.includes('No camera')){
            return 'No camera was found on this device/browser.'
        }

        return `Camera/scanner error: ${message}`
    }

    function startUtangScanner(cameraConfig){
        return createUtangScanner().start(
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
                if(decodedText === lastScanText){
                    return
                }

                lastScanText = decodedText
                fillUtangProduct(decodedText)
                stopUtangScanner()
            },
            (errorMessage, error) => {
                if(error && error.type !== 2){
                    console.debug(errorMessage, error)
                }
            }
        )
    }

    startScannerBtn.addEventListener('click', function(){
        if(typeof Html5Qrcode === 'undefined'){
            scanStatus.textContent = 'Barcode scanner failed to load. Check your internet connection and try again.'
            scanStatus.classList.remove('text-muted', 'text-success')
            scanStatus.classList.add('text-danger')
            return
        }

        scannerReader.classList.remove('d-none')
        startScannerBtn.classList.add('d-none')
        stopScannerBtn.classList.remove('d-none')
        scanStatus.textContent = 'Starting camera...'
        scanStatus.classList.remove('text-danger', 'text-success')
        scanStatus.classList.add('text-muted')

        Html5Qrcode.getCameras()
            .then(cameras => {
                scanStatus.textContent = `${cameras.length} camera(s) found. Starting scanner...`

                if(cameras.length){
                    return startUtangScanner(getBackCameraId(cameras))
                }

                return startUtangScanner({
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                })
            })
            .then(() => {
                scannerIsRunning = true
                let seconds = 0
                scanStatus.textContent = 'Camera started. Move closer until the barcode lines are sharp.'
                clearInterval(scanTimer)
                scanTimer = setInterval(() => {
                    seconds += 2
                    scanStatus.textContent = `Scanning... ${seconds}s. Keep the barcode flat, bright, and sharp.`
                }, 2000)
            })
            .catch(firstErr => {
                console.error(firstErr)

                startUtangScanner({
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                })
                    .then(() => {
                        scannerIsRunning = true
                        let seconds = 0
                        scanStatus.textContent = 'Camera started. Move closer until the barcode lines are sharp.'
                        clearInterval(scanTimer)
                        scanTimer = setInterval(() => {
                            seconds += 2
                            scanStatus.textContent = `Scanning... ${seconds}s. Keep the barcode flat, bright, and sharp.`
                        }, 2000)
                    })
                    .catch(err => {
                        console.error(err)
                        scannerIsRunning = false
                        scannerReader.classList.add('d-none')
                        startScannerBtn.classList.remove('d-none')
                        stopScannerBtn.classList.add('d-none')
                        scanStatus.textContent = cameraErrorMessage(err)
                        scanStatus.classList.remove('text-muted', 'text-success')
                        scanStatus.classList.add('text-danger')
                    })
            })
    })

    stopScannerBtn.addEventListener('click', stopUtangScanner)
    addUtangModal.addEventListener('show.bs.modal', function(event){
        const button = event.relatedTarget

        if(!button){
            return
        }

        utangPersonIdInput.value = button.getAttribute('data-person-id')
        utangPersonName.textContent = button.getAttribute('data-person-name')
    })
    addUtangModal.addEventListener('hidden.bs.modal', function(){
        stopUtangScanner()
        addUtangForm.reset()
        qtyInput.value = 1
        utangPersonName.textContent = 'Name'
        utangPersonIdInput.value = ''
        scanStatus.textContent = 'Scan a product barcode to autofill item and price.'
        scanStatus.classList.remove('text-danger', 'text-success')
        scanStatus.classList.add('text-muted')
        updateUtangTotal()
    })
    barcodeInput.addEventListener('input', function(){
        if(barcodeProducts[this.value.trim()]){
            fillUtangProduct(this.value)
        }
    })
    barcodeInput.addEventListener('change', function(){
        fillUtangProduct(this.value)
    })
    barcodeFileInput.addEventListener('change', function(){
        if(!this.files.length){
            return
        }

        if(typeof Html5Qrcode === 'undefined'){
            scanStatus.textContent = 'Barcode scanner failed to load.'
            scanStatus.classList.remove('text-muted', 'text-success')
            scanStatus.classList.add('text-danger')
            return
        }

        createUtangScanner().scanFile(this.files[0], true)
            .then(decodedText => {
                fillUtangProduct(decodedText)
            })
            .catch(err => {
                console.error(err)
                scanStatus.textContent = 'Could not read a barcode from that image. Try a brighter, sharper photo.'
                scanStatus.classList.remove('text-muted', 'text-success')
                scanStatus.classList.add('text-danger')
            })
            .finally(() => {
                this.value = ''
            })
    })
    qtyInput.addEventListener('input', updateUtangTotal)
    priceInput.addEventListener('input', updateUtangTotal)
    updateUtangTotal()

    const posForm = document.getElementById('pos-form')
    const posProductIdInput = document.getElementById('pos-product-id')
    const posBarcodeInput = document.getElementById('pos-barcode')
    const posItemInput = document.getElementById('pos-item')
    const posQtyInput = document.getElementById('pos-qty')
    const posPriceInput = document.getElementById('pos-price')
    const posStockInput = document.getElementById('pos-stock')
    const posTotalText = document.getElementById('pos-total')
    const posSaveBtn = document.getElementById('pos-save')
    const posScanStatus = document.getElementById('pos-scan-status')
    const startPosScannerBtn = document.getElementById('start-pos-scanner')
    const stopPosScannerBtn = document.getElementById('stop-pos-scanner')
    const posBarcodeFileInput = document.getElementById('pos-barcode-file')
    const posScannerReader = document.getElementById('pos-barcode-reader')
    let posScanner = null
    let posScannerIsRunning = false
    let posScanTimer = null
    let lastPosScanText = ''

    function setPosStatus(message, type = 'muted'){
        posScanStatus.textContent = message
        posScanStatus.classList.remove('text-muted', 'text-success', 'text-danger')
        posScanStatus.classList.add(`text-${type}`)
    }

    function updatePosTotal(){
        const qty = parseFloat(posQtyInput.value) || 0
        const price = parseFloat(posPriceInput.value) || 0
        posTotalText.textContent = formatPeso(qty * price)
    }

    function fillPosProduct(barcode){
        const cleanBarcode = barcode.trim()
        const product = barcodeProducts[cleanBarcode]

        posBarcodeInput.value = cleanBarcode

        if(!cleanBarcode){
            setPosStatus('Scan or type a product barcode.')
            posProductIdInput.value = ''
            posItemInput.value = ''
            posPriceInput.value = ''
            posStockInput.value = ''
            posSaveBtn.disabled = true
            updatePosTotal()
            return
        }

        if(!product){
            setPosStatus(`Barcode ${cleanBarcode} was not found in products.`, 'danger')
            posProductIdInput.value = ''
            posItemInput.value = ''
            posPriceInput.value = ''
            posStockInput.value = ''
            posSaveBtn.disabled = true
            updatePosTotal()
            return
        }

        posProductIdInput.value = product.id
        posItemInput.value = product.item
        posPriceInput.value = product.price
        posStockInput.value = product.stock_quantity
        posQtyInput.max = product.stock_quantity
        posQtyInput.value = posQtyInput.value || 1
        posSaveBtn.disabled = product.stock_quantity <= 0
        setPosStatus(`Found ${product.item}. Stock: ${product.stock_quantity}.`, product.stock_quantity > 0 ? 'success' : 'danger')
        updatePosTotal()
    }

    function stopPosScanner(){
        clearInterval(posScanTimer)

        if(!posScanner || !posScannerIsRunning){
            posScannerReader.classList.add('d-none')
            startPosScannerBtn.classList.remove('d-none')
            stopPosScannerBtn.classList.add('d-none')
            return Promise.resolve()
        }

        return posScanner.stop()
            .then(() => {
                posScannerIsRunning = false
                posScannerReader.classList.add('d-none')
                startPosScannerBtn.classList.remove('d-none')
                stopPosScannerBtn.classList.add('d-none')
            })
            .catch(err => {
                console.error(err)
            })
    }

    function createPosScanner(){
        if(posScanner){
            return posScanner
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

        posScanner = new Html5Qrcode('pos-barcode-reader', {
            formatsToSupport: formats,
            useBarCodeDetectorIfSupported: true,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        })

        return posScanner
    }

    function startPosScanner(cameraConfig){
        return createPosScanner().start(
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
                if(decodedText === lastPosScanText){
                    return
                }

                lastPosScanText = decodedText
                fillPosProduct(decodedText)
                stopPosScanner()
            },
            (errorMessage, error) => {
                if(error && error.type !== 2){
                    console.debug(errorMessage, error)
                }
            }
        )
    }

    startPosScannerBtn.addEventListener('click', function(){
        if(typeof Html5Qrcode === 'undefined'){
            setPosStatus('Barcode scanner failed to load.', 'danger')
            return
        }

        posScannerReader.classList.remove('d-none')
        startPosScannerBtn.classList.add('d-none')
        stopPosScannerBtn.classList.remove('d-none')
        setPosStatus('Starting camera...')

        Html5Qrcode.getCameras()
            .then(cameras => {
                if(cameras.length){
                    return startPosScanner(getBackCameraId(cameras))
                }

                return startPosScanner({
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                })
            })
            .then(() => {
                let seconds = 0
                posScannerIsRunning = true
                setPosStatus('Camera started. Move closer until the barcode lines are sharp.')
                clearInterval(posScanTimer)
                posScanTimer = setInterval(() => {
                    seconds += 2
                    setPosStatus(`Scanning... ${seconds}s. Keep the barcode flat, bright, and sharp.`)
                }, 2000)
            })
            .catch(err => {
                console.error(err)
                posScannerIsRunning = false
                posScannerReader.classList.add('d-none')
                startPosScannerBtn.classList.remove('d-none')
                stopPosScannerBtn.classList.add('d-none')
                setPosStatus(cameraErrorMessage(err), 'danger')
            })
    })

    stopPosScannerBtn.addEventListener('click', stopPosScanner)
    posBarcodeInput.addEventListener('input', function(){
        if(barcodeProducts[this.value.trim()]){
            fillPosProduct(this.value)
        }
    })
    posBarcodeInput.addEventListener('change', function(){
        fillPosProduct(this.value)
    })
    posBarcodeFileInput.addEventListener('change', function(){
        if(!this.files.length){
            return
        }

        if(typeof Html5Qrcode === 'undefined'){
            setPosStatus('Barcode scanner failed to load.', 'danger')
            return
        }

        createPosScanner().scanFile(this.files[0], true)
            .then(decodedText => {
                fillPosProduct(decodedText)
            })
            .catch(err => {
                console.error(err)
                setPosStatus('Could not read a barcode from that image. Try a brighter, sharper photo.', 'danger')
            })
            .finally(() => {
                this.value = ''
            })
    })
    posQtyInput.addEventListener('input', function(){
        const stock = parseInt(posStockInput.value) || 0
        const qty = parseInt(posQtyInput.value) || 0
        posSaveBtn.disabled = !posProductIdInput.value || qty < 1 || qty > stock

        if(qty > stock){
            setPosStatus('Quantity is higher than current stock.', 'danger')
        }else if(posProductIdInput.value){
            setPosStatus(`Found ${posItemInput.value}. Stock: ${stock}.`, 'success')
        }

        updatePosTotal()
    })
    posForm.addEventListener('submit', function(e){
        const stock = parseInt(posStockInput.value) || 0
        const qty = parseInt(posQtyInput.value) || 0

        if(!posProductIdInput.value || qty < 1 || qty > stock){
            e.preventDefault()
            setPosStatus('Choose a valid product and quantity before saving.', 'danger')
        }
    })
    updatePosTotal()


    //logout
    function logout(){
        if(confirm('Are you sure you want to logout?')){
            window.location.href = "{{ route('logout') }}"
        }
    }
</script>

@endsection
