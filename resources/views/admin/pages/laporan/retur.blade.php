@extends('admin.layout.template')

@section('title', 'Detail Retur')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('salesorder.laporan') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>Detail Retur</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="small">Tanggal Kirim</div>
                    {{ $data->tanggal_kirim }}
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="small">Nama Customer</div>
                        {{ $data->nama }}
                    </div>
                </div>
                <div class="col">
                    <div class="small">Kode</div>
                    {{ $data->kode }}
                </div>
                <div class="col">
                    <div class="small">Keterangan</div>
                    {{ $data->keterangan }}
                </div>
                <div class="col">
                    <div class="small">Alamat</div>
                    {{ $data->alamat }}
                </div>
                <div class="col">
                    <div class="small">Telepon</div>
                    {{ $data->telp }}
                </div>
            </div>
        </div>
    </section>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <table class="table default-table">
                         <thead>
                            <tr>
                                <th width=10px>No</th>
                                <th>Nama Item</th>
                                <th>Qty</th>
                                <th>Qty Fulfillment</th>
                                <th>Berat</th>
                                <th>Berat Fulfillment</th>
                                <th>Select</th>
                                <th>Retur Qty</th>
                                <th>Retur Berat</th>
                                <th>Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                                $berat = 0;
                            @endphp
                            @foreach ($list as $i => $row)
                                @php
                                    $total += $row->qty;
                                    $berat += $row->berat;
                                @endphp
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $row->nama_detail }}</td>
                                    <td>{{ $row->qty }}</td>
                                    <td>{{ $row->fulfillment_qty }}</td>
                                    <td>{{ $row->berat }}</td>
                                    <td>{{ $row->fulfillment_berat }}</td>
                                    <td>
                                        <div class="form-group">
                                            <select name="returto" class="form-control" id="returto{{ $row->id }}">
                                                <option value="" disabled selected hidden>Pilih </option>
                                                <option value="gudang">Gudang</option>
                                                <option value="chiller">Chiller</option>
                                                <option value="musnahkan">Musnahkan</option>
                                                <option value="frozen">Frozen</option>
                                            </select>
                                            @error('returto') <div class="small text-danger">{{ message }}</div>
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" name="returqty" class="form-control"
                                                id="returqty{{ $row->id }}" min="1" max="{{ $row->qty }}"
                                                placeholder="Tuliskan " value="" autocomplete="off">
                                            @error('returqty') <div class="small text-danger">{{ message }}</div>
                                            @enderror
                                        </div>

                                    </td>
                                    <td>
                                        <div class="form-group">

                                            <input type="number" name="returberat" class="form-control"
                                                id="returberat{{ $row->id }}" min="1" max="{{ $row->berat }}"
                                                placeholder="Tuliskan " value="" autocomplete="off">
                                            @error('returberat') <div class="small text-danger">{{ message }}</div>
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="alasan" class="form-control"
                                                id="alasan{{ $row->id }}" placeholder="Tuliskan " value=""
                                                autocomplete="off">
                                            @error('alasan') <div class="small text-danger">{{ message }}</div>
                                            @enderror
                                        </div>

                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-block btn-sm goretur"
                                            data-item="{{ $row->id }}"
                                            data-order="{{ $row->order_id }}">Ajukan</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>Total Qty : {{ $total }}</div>
                    <div>Total Berat : {{ $berat }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $(document).on('click', '.goretur', function() {
                var item = $(this).data('item');
                var order = $(this).data('order');
                var qty = $('#returqty' + item).val();
                var berat = $('#returberat' + item).val();
                var alasan = $('#alasan' + item).val();
                var tujuan = $('#returto' + item + ' option:selected').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('salesorder.returadd') }}",
                    method: "POST",
                    data: {
                        item: item,
                        order: order,
                        tujuan: tujuan,
                        qty: qty,
                        alasan: alasan,
                        berat: berat
                    },
                    success: function(data) {
                        swal({
                            title: "Success!",
                            type: "success",
                            showCancelButton: false,
                            showConfirmButton: false,
                            timer: 2000
                        }, function() {
                            swal.close();
                            location.reload();
                        });

                    }
                });
            })
        });

    </script>
@stop
