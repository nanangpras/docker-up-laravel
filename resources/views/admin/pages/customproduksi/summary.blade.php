
  @extends('admin.layout.template')

  @section('title', 'Custom Produksi')

  @section('content')
      <div class="row mb-4">
          <div class="col">
              <a href="{{ route('kepalaregu.index') }}" class="btn btn-outline btn-sm btn-back">
                <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>
          <div class="col text-center">
              <b>CUSTOM PRODUKSI</b>
          </div>
          <div class="col"></div>
      </div>

      <section class="panel">
          <div class="card-body">

            @foreach($order as $data)

            <div class="card mb-2">
                <div class="card-body">

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
                            <div class="small">No SO</div>
                            {{ $data->no_so }}
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

                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th width=10px>No</th>
                                <th>Nama Item</th>
                                <th>Qty Order</th>
                                <th>Berat Order</th>
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
                            @foreach ($data->daftar_order_full as $i => $row)
                                @php
                                    $total += $row->qty;
                                    $berat += $row->berat;
                                @endphp
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $row->nama_detail }}</td>
                                    <td>{{ $row->qty }}</td>
                                    <td>{{ $row->berat }}</td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>


                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>Total Qty : {{ $total }}</div>
                    <div>Total Berat : {{ $berat }}</div>

                </div>
            </div>

        @endforeach

          </div>
      </section>

      <style>
          .button-menu a{
              margin-bottom: 15px;
          }
      </style>
  @stop


