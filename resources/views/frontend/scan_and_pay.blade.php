@extends('frontend.layouts.app')
@section('title', 'Scan And Pay')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body ">
                        <div class="text-center">
                            <img src="{{ asset('images/scan_and_pay.jpg') }}" alt="" width="400px">
                        </div>
                        <p class=" text-center">Click Scan button to scan QR and pay</p>
                        @error('fail')
                            <x-alert status='{{"fail"}}' message="{{ $message }}"></x-alert>
                        @enderror
                        <div class=" text-center">
                            <button class=" btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">Scan</button>

                            <!--Scan Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Scan Attendance QR</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <video id="video" width="100%" height="300px"></video>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            const myModalEl = document.getElementById('exampleModal');
            const myModal = new Modal('#exampleModal');

            const qrScanner = new QrScanner(
                document.getElementById('video'),
                result => {
                    if (result) {
                        myModal.hide();
                        qrScanner.stop();

                        window.location.replace(`/scan-and-pay-form?to_phone=${result}`);
                    }
                }
            );

            myModalEl.addEventListener('show.bs.modal', event => {
                qrScanner.start();
            });
            myModalEl.addEventListener('hide.bs.modal', event => {
                qrScanner.stop();
            });
        });
    </script>
@endpush
