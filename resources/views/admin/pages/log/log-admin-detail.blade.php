<div class="table-responsive">

<table class="table default-table">
    <thead>

        @if ($log_admin->activity == 'evis')
            <tr>
                <th colspan="2">Item lama</th>
                <th colspan="2">Item Baru</th>
            </tr>
        @else
            <tr>
                <th>Nama</th>
                <th>Data</th>
            </tr>
        @endif
    </thead>
    <tbody>
        @php
            $data       = json_decode($log_admin->data,true);
        @endphp
        @if ($log_admin->activity == 'sales_order')
            <tr>
                <td>Nomer So</td>
                <td>{{$data['header']['no_so'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Nomer DO</td>
                <td>{{$data['header']['no_do'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Nama Customer</td>
                <td>{{$data['header']['nama'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>{{$data['header']['alamat_kirim'] ?? ''}}</td>
            </tr>
        @elseif($log_admin->activity == 'sales_order_reset')
            @for ($rst = 0; $rst < count($data['header']) ; $rst++)
                <tr>
                    <td>Nama</td>
                    <td>{{$data['header'][$rst]['nama'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Nomer DO</td>
                    <td>
                        <a href="{{ route('chiller.show', $data['header'][$rst]['chiller_alokasi']) }}"target="_blank">{{ $data['header'][$rst]['chiller_alokasi'] }}</a>
                    </td>
                </tr>
                <tr>
                    <td>No Chiller</td>
                    <td>{{$data['header'][$rst]['no_do'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Berat</td>
                    <td>{{$data['header'][$rst]['bb_berat'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Qty</td>
                    <td>{{$data['header'][$rst]['bb_item'] ?? ''}}</td>
                </tr>
            @endfor
        @elseif($log_admin->activity == 'security')
            @if ($log_admin->type == 'edit-security')
                <tr>
                    <td>Catatan</td>
                    <td>{{$log_admin->content ?? ''}}</td>
                </tr>
                <tr>
                    <td>Tanggal Potong</td>
                    <td>{{$data['data']['prod_tanggal_potong'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Supir</td>
                    <td>{{$data['data']['sc_pengemudi'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>No PO</td>
                    <td>{{$data['data']['no_po'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>No DO</td>
                    <td>{{$data['data']['no_do'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Jam Masuk</td>
                    <td>{{$data['data']['sc_jam_masuk'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Ekor</td>
                    <td>{{$data['data']['sc_ekor_do'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Berat</td>
                    <td>{{$data['data']['sc_berat_do'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>NOPOL</td>
                    <td>{{$data['data']['sc_no_polisi'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Kandang</td>
                    <td>{{$data['data']['sc_nama_kandang'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>{{$data['data']['sc_alamat_kandang'] ?? ''}}</td>
                </tr>
            @else
                <tr>
                    <td>Tanggal</td>
                    <td>{{$data['tanggal'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Supir</td>
                    <td>{{$data['supir'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Urut</td>
                    <td>{{$data['nourut'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Jam Masuk</td>
                    <td>{{$data['sc_jam_masuk'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Ekor</td>
                    <td>{{$data['ekor_do'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Berat</td>
                    <td>{{$data['berat_do'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>NOPOL</td>
                    <td>{{$data['no_polisi'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Kandang</td>
                    <td>{{$data['nama_kandang'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>{{$data['alamat_kandang'] ?? ''}}</td>
                </tr>
            @endif

        @elseif($log_admin->activity == 'lpah')
            <tr>
                <td>Jam Bongkar</td>
                <td>{{$data['header']['lpah_jam_bongkar'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Tanggal potong</td>
                <td>{{$data['header']['lpah_tanggal_potong'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Keranjang</td>
                <td>{{$data['header']['lpah_jumlah_keranjang'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat keranjang</td>
                <td>{{$data['header']['berat_keranjang'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat</td>
                <td>{{$data['header']['berat_lpah'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat Isi</td>
                <td>{{$data['header']['berat_isi'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat bersih</td>
                <td>{{$data['header']['berat_bersih_lpah'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{$data['header']['total_bersih_lpah'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Ukuran Ayam</td>
                <td>{{$data['header']['prodpur']['jenis_ayam'] ?? ''}} - ( {{$data['header']['prodpur']['ukuran_ayam'] ?? ''}} )</td>
            </tr>
        @elseif($log_admin->activity == 'grading')
            <tr>
                <td>Tanggal Lama</td>
                <td>
                    {{ $data['data_lama'] }}
                </td>
            </tr>
            <tr>
                <td>
                    Tanggal Baru
                </td>
                <td>
                    {{ $data['data_baru'] }}
                </td>
            </tr>
        @elseif($log_admin->activity == 'evis')
            <tr>
                <td>Qty</td>
                <td>{{$data['before_update']['qty'] ?? ''}}</td>
                <td>Qty</td>
                <td>{{$data['after_update']['qty'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat</td>
                <td>{{$data['before_update']['berat'] ?? ''}}</td>
                <td>Berat</td>
                <td>{{$data['after_update']['berat'] ?? ''}}</td>
            </tr>
        @elseif($log_admin->activity == 'checker' && $log_admin->type == 'edit')
            <tr>
                <td>Item Lama</td>
                <td>
                    <table>
                        <tr>
                            <td>Berat Item</td>
                            <td>{{$data['item_lama']['berat_item'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>Stok Item</td>
                            <td>{{$data['item_lama']['berat_stock'] ?? ''}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Item Baru</td>
                <td>
                    <table>
                        <tr>
                            <td>Berat Item</td>
                            <td>{{$data['item_baru']['berat_item'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>Stok Item</td>
                            <td>{{$data['item_baru']['berat_stock'] ?? ''}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @elseif($log_admin->activity == 'checker' && ($log_admin->type == 'tambah' || $log_admin->type == 'hapus'))
            <tr>
                <td>Nama</td>
                <td>{{$data['graditem']['nama'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Tanggal Potong</td>
                <td>{{$data['tanggal_potong'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Qty</td>
                <td>{{$data['stock_item'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat</td>
                <td>{{$data['stock_berat'] ?? ''}}</td>
            </tr>
        @elseif($log_admin->activity == 'retur' || $log_admin->activity == 'Edit Penanganan')
            <tr>
                <td>Item</td>
                <td>{{$data['data']['item_id'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Order Item</td>
                <td>{{$data['data']['orderitem_id'] ?? ''}}</td>
            </tr>
            <tr>
                <td>SKU</td>
                <td>{{$data['data']['sku'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Catatan</td>
                <td>{{$data['data']['catatan'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Berat</td>
                <td>{{$data['data']['berat'] ?? ''}}</td>
            </tr>
            <tr>
                <td>Penanganan</td>
                <td>{{$data['data']['penanganan'] ?? ''}}</td>
            </tr>
        @elseif($log_admin->activity == 'pr')
            @if ($log_admin->type == 'input')
                <tr>
                    <td>Nomer PO</td>
                    <td>{{$data['document_number'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Subsidiary</td>
                    <td>{{$data['po_subsidiary'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Vendor</td>
                    <td>{{$data['vendor_name'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Tipe PO</td>
                    <td>{{$data['type_po'] ?? ''}}</td>
                </tr>

            @elseif ($log_admin->type == 'update')
                <tr>
                    <td>Nomer PO</td>
                    <td>{{$data['no_po'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Qty</td>
                    <td>{{$data['qty'] ?? ''}}</td>
                </tr>
            @endif
        @elseif($log_admin->activity == 'marketing')
            @if ($log_admin->type == 'edit' || $log_admin->type == 'data' || $log_admin->type == 'hapus' )
                <tr>
                    <td>Nomer SO</td>
                    <td>{{$data['header']['no_so'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Memo</td>
                    <td>{{$data['header']['memo'] ?? ''}}</td>
                </tr>
                @for ($i = 0; $i < count($data['list']); $i++)
                    <tr>
                        <td>Item</td>
                        <td>{{$data['list'][$i]['item_nama'] ?? ''}}</td>
                    </tr>
                    <tr>
                        <td>Berat</td>
                        <td>{{$data['list'][$i]['berat'] ?? ''}}</td>
                    </tr>
                    <tr>
                        <td>Qty</td>
                        <td>{{$data['list'][$i]['qty'] ?? ''}}</td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td>Rp {{$data['list'][$i]['harga'] ?? ''}}</td>
                    </tr>
                @endfor
            @endif
        @elseif($log_admin->activity == 'pembelian')
            @if ($log_admin->type == 'edit' || $log_admin->type == 'data' )
                <tr>
                    <td>Nomer</td>
                    <td>{{$data['header']['document_number'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Tipe</td>
                    <td>{{$data['header']['type_po'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Tanggal Kirim</td>
                    <td>{{$data['header']['tanggal_kirim'] ?? ''}}</td>
                </tr>
                <tr>
                    <td>Memo</td>
                    <td>{{$data['header']['keterangan'] ?? ''}}</td>
                </tr>
                @for ($i = 0; $i < count($data['list']); $i++)
                    @php
                        $item_name = \App\Models\Item::find($data['list'][$i]['item_id']);
                    @endphp
                    <tr>
                        <td>Item</td>
                        <td>{{$item_name->nama ?? ''}}</td>
                    </tr>
                    <tr>
                        <td>Berat</td>
                        <td>{{$data['list'][$i]['berat'] ?? ''}}</td>
                    </tr>
                    <tr>
                        <td>Qty</td>
                        <td>{{$data['list'][$i]['qty'] ?? ''}}</td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td>Rp {{$data['list'][$i]['harga'] ?? ''}}</td>
                    </tr>
                @endfor
            @endif
        @elseif($log_admin->activity == 'cut_off')
            @php
                $decoded    = json_decode($log_admin->data);
                $affected   = $decoded->affected_data;
                $start_date = $affected->tanggal_awal;
                $end_date   = $affected->tanggal_akhir;
                // if(isset($affected->affected_id_abf)){
                //     if($affected->affected_id_abf != ''){
                //         $idABF      = implode(",",$affected->affected_id_abf);
                //     }
                // }
                // if(isset($affected->affected_id_chiller)){
                //     if($affected->affected_id_chiller != ''){
                //         $idChiller  = implode(",",$affected->affected_id_chiller);
                //     }
                // }
            @endphp
            <tr>
                <td>Tabel Name</td>
                <td>{{$log_admin->table_name}}</td>
            </tr>
            <tr>
                <td>Activity</td>
                <td>{{$log_admin->activity}}</td>
            </tr>
            <tr>
                <td>Tanggal Awal</td>
                <td>{{ $start_date ?? '' }}</td>
            </tr>
            <tr>
                <td>Tanggal Akhir</td>
                <td>{{ $end_date ?? ''}}</td>
            </tr>
            {{--@isset($idABF)
            <tr>
                <td>Data ABF</td>
                <td>{{ $idABF ?? '' }}</td>
            </tr>
            @endisset
            @isset($idChiller)
            <tr>
                <td>Data Chiller</td>
                <td>{{ $idChiller ?? '' }}</td>
            </tr>
            @endisset
            --}}
        @elseif($log_admin->activity == 'admin' && $log_admin->type == 'edit' && $log_admin->table_name == 'items' && $log_admin->content == 'Edit Item')
            <tr>
                <td>Item Lama</td>
                <td>
                    <table>
                        <tr>
                            <td>Nama Item</td>
                            <td>{{$data['item_lama']['nama'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>SKU</td>
                            <td>{{$data['item_lama']['sku'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>
                                @foreach ($category as $item)
                                    @if ($item->id == $data['item_lama']['category_id'] )
                                        {{$item->nama}}
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td>Subsidiary</td>
                            <td>{{$data['item_lama']['subsidiary'] ?? ''}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Item Baru</td>
                <td>
                    <table>
                        <tr>
                            <td>Nama Item</td>
                            <td>{{$data['item_baru']['nama'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>SKU</td>
                            <td>{{$data['item_baru']['sku'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>
                                @foreach ($category as $item)
                                    @if ($item->id == $data['item_baru']['category_id'] )
                                        {{$item->nama}}
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td>Subsidiary</td>
                            <td>{{$data['item_baru']['subsidiary'] ?? ''}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @elseif($log_admin->type == 'delete' && $log_admin->activity == 'kepala_regu_bb')
                    <tr>
                        <td>User</td>
                        <td>{{ $log_admin->getUser->name }}</td>
                    </tr>
                    <tr>
                        @php
                            $jsonData = json_decode($log_admin->data, true);
                        @endphp
                            <tr>
                                <td>ID</td>
                                <td>{{ $jsonData['data']['id'] }}</td>
                            </tr>
                            <tr>
                                <td>Freestock ID</td>
                                <td>{{ $jsonData['data']['freestock_id'] ?? $jsonData['data']['id'] }}</td>
                            </tr>
                            @if(isset($jsonData['data']['chiller_id']))
                                <tr>
                                    <td>Item ID</td>
                                    <td>{{ $jsonData['data']['chiller_id'] }}</td>
                                </tr>
                            @elseif(isset($jsonData['data']['item_id']))
                                <tr>
                                    <td>Nama Produk</td>
                                    <td>{{ $jsonData['data']['prod_nama'] }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Produksi</td>
                                    <td>{{ $jsonData['data']['tanggal_produksi'] }}</td>
                                </tr>
                            @else
                            @endif
                            @if(isset($jsonData['data']['freestock_id']))
                            <tr>
                                <td>Qty</td>
                                <td>{{ $jsonData['data']['qty'] }}</td>
                            </tr>
                            <tr>
                                <td>Berat</td>
                                <td>{{ $jsonData['data']['berat'] }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>Tanggal Hapus</td>
                                <td>
                                {{$log_admin->created_at}}
                                </td>
                            </tr>
                    </tr>
        @elseif($log_admin->activity == 'SOH')
            <tr>
                <td>User</td>
                <td>{{ $log_admin->getUser->name }}</td>
            </tr>
            <tr>
                <td>Activity</td>
                <td>{{ $log_admin->activity }}</td>
            </tr>
            <tr>
                <td>Nama Item</td>
                <td>{{ $data['before_update']['nama'] }}</td>
            </tr>
            <tr>
                <td>Before Edit</td>
                <td>
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>Qty Awal</td>
                                <td>{{ $data['before_update']['qty_awal'] }}</td>
                            </tr>
                            <tr>
                                <td>Berat Awal</td>
                                <td>{{ $data['before_update']['berat_awal'] }}</td>
                            </tr>
                            <tr>
                                <td>Qty Sisa</td>
                                <td>{{ $data['before_update']['qty'] }}</td>
                            </tr>
                            <tr>
                                <td>Berat Sisa</td>
                                <td>{{ $data['before_update']['berat'] }}</td>
                            </tr>
                            <tr>
                                <td>Subpack</td>
                                <td>{{ $data['before_update']['subpack'] }}</td>
                            </tr>
                            <tr>
                                <td>Id Gudang</td>
                                <td>{{ $data['before_update']['gudang_id'] }}</td>
                            </tr>
                            <tr>
                                <td>Asal ABF</td>
                                <td>{{ $data['before_update']['asal_abf'] }}</td>
                            </tr>
                            <tr>
                                <td>Barang Titipan</td>
                                <td>{{ $data['before_update']['barang_titipan'] }}</td>
                            </tr>
                            <tr>
                                <td>Isi Karung</td>
                                <td>{{ $data['before_update']['karung_isi'] }}</td>
                            </tr>
                            <tr>
                                <td>Qty Karung</td>
                                <td>{{ $data['before_update']['karung_qty'] }}</td>
                            </tr>
                            <tr>
                                <td>Packaging</td>
                                <td>{{ $data['before_update']['packaging'] }}</td>
                            </tr>
                            <tr>
                                <td>Karung</td>
                                <td>{{ $data['before_update']['karung'] }}</td>
                            </tr>
                            <tr>
                                <td>Plasik</td>
                                <td>{{ $data['before_update']['plastik_group'] }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Produksi</td>
                                <td>{{ $data['before_update']['production_date'] }}</td>
                            </tr>
                            <tr>
                                <td>Parting</td>
                                <td>{{ $data['before_update']['parting'] }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td>After Edit</td>
                <td>
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>Qty Awal</td>
                                <td>{{ $data['after_update']['qty_awal'] }}</td>
                            </tr>
                            <tr>
                                <td>Berat Awal</td>
                                <td>{{ $data['after_update']['berat_awal'] }}</td>
                            </tr>
                            <tr>
                                <td>Qty Sisa</td>
                                <td>{{ $data['after_update']['qty'] }}</td>
                            </tr>
                            <tr>
                                <td>Berat Sisa</td>
                                <td>{{ $data['after_update']['berat'] }}</td>
                            </tr>
                            <tr>
                                <td>Subpack</td>
                                <td>{{ $data['after_update']['subpack'] }}</td>
                            </tr>
                            <tr>
                                <td>Id Gudang</td>
                                <td>{{ $data['after_update']['gudang_id'] }}</td>
                            </tr>
                            <tr>
                                <td>Asal ABF</td>
                                <td>{{ $data['after_update']['asal_abf'] }}</td>
                            </tr>
                            <tr>
                                <td>Barang Titipan</td>
                                <td>{{ $data['after_update']['barang_titipan'] }}</td>
                            </tr>
                            <tr>
                                <td>Isi Karung</td>
                                <td>{{ $data['after_update']['karung_isi'] }}</td>
                            </tr>
                            <tr>
                                <td>Qty Karung</td>
                                <td>{{ $data['after_update']['karung_qty'] }}</td>
                            </tr>
                            <tr>
                                <td>Packaging</td>
                                <td>{{ $data['after_update']['packaging'] }}</td>
                            </tr>
                            <tr>
                                <td>Karung</td>
                                <td>{{ $data['after_update']['karung'] }}</td>
                            </tr>
                            <tr>
                                <td>Plasik</td>
                                <td>{{ $data['after_update']['plastik_group'] }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Produksi</td>
                                <td>{{ $data['after_update']['production_date'] }}</td>
                            </tr>
                            <tr>
                                <td>Parting</td>
                                <td>{{ $data['after_update']['parting'] }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        @else
            <tr>
                <td>{{$log_admin->data}}</td>
            </tr>
        @endif
        <tr>
        </tr>
    </tbody>
</table>
</div>

