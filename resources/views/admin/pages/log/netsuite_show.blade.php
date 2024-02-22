@extends('admin.layout.template')

@section('title', 'Detail Data Netsuite')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('sync.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Detail Data Netsuite</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <form action="{{ route('sync.postsync', $data->id) }}" method="post">
            @csrf
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Record Type
                        <input type="text" class="form-control" name="record_type" value="{{ $data->record_type }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        Label
                        <input type="text" class="form-control" name="label" value="{{ $data->label }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        Tanggal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="trans_date" value="{{ $data->trans_date }}"
                            class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Table
                        <input type="text" class="form-control" name="tabel" value="{{ $data->tabel }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        Table ID
                        <input type="text" class="form-control" name="tabel_id" value="{{ $data->tabel_id }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        Paket ID
                        <input type="text" class="form-control" name="paket_id" value="{{ $data->paket_id }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Location
                        <input type="text" class="form-control" name="location" value="{{ $data->location }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        ID Location
                        <input type="text" class="form-control" name="id_location" value="{{ $data->id_location }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Subsidiary
                        <input type="text" class="form-control" name="subsidiary" value="{{ $data->subsidiary }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        ID Subsidiary
                        <input type="text" class="form-control" name="subsidiary_id" value="{{ $data->subsidiary_id }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Script
                        <input type="text" class="form-control" name="script" value="{{ $data->script }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        Deploy
                        <input type="text" class="form-control" name="deploy" value="{{ $data->deploy }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        Status
                        <input type="text" class="form-control" name="status" value="{{ $data->status }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div id="data-content" style="display: none">
                    {{ $data->data_content }}
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Data</label>
                    <div class="col-sm-12">
                        <div id="legend">
                            <span id="expander">Expand all</span>
                            {{-- <span class="array">array</span>
                            <span class="object">object</span>
                            <span class="string">string</span>
                            <span class="number">number</span>
                            <span class="boolean">boolean</span>
                            <span class="null">null</span> --}}
                        </div>

                        <div id="editor" class="json-editor"></div>

                        <label for="json">Or paste JSON directly here:</label>
                        <p id="json-note">Note that you can edit your JSON directly in the textarea below.
                            The JSON viewer will get updated when you leave the field.</p>
                        <textarea id="json" name="data_content" class="form-control"
                            style="width: 80%; height: 300px"></textarea><br />
                        <script type="text/javascript">
                            var json = JSON.parse($('#data-content').html());
                
                                  function printJSON() {
                                      $('#json').val(JSON.stringify(json));
                
                                  }
                
                                  function updateJSON(data) {
                                      json = data;
                                      printJSON();
                                  }
                
                                  function showPath(path) {
                                      $('#path').text(path);
                                  }
                
                                  function syncData(){
                                      var val = $('#json').val();
                
                                          if (val) {
                                              try { json = JSON.parse(val); }
                                              catch (e) { alert('Error in parsing json. ' + e); }
                                          } else {
                                              json = {};
                                          }
                
                                          $('#editor').jsonEditor(json, { change: updateJSON, propertyclick: showPath });
                                  }
                
                                  $(document).ready(function() {
                
                                      $('#rest > button').click(function() {
                                          var url = $('#rest-url').val();
                                          $.ajax({
                                              url: url,
                                              dataType: 'jsonp',
                                              jsonp: $('#rest-callback').val(),
                                              success: function(data) {
                                                  json = data;
                                                  $('#editor').jsonEditor(json, { change: updateJSON, propertyclick: showPath });
                                                  printJSON();
                                              },
                                              error: function() {
                                                  alert('Something went wrong, double-check the URL and callback parameter.');
                                              }
                                          });
                                      });
                
                                      $('#json').change(function() {
                                          syncData();
                                      });
                
                                      $('#expander').click(function() {
                                          var editor = $('#editor');
                                          editor.toggleClass('expanded');
                                          $(this).text(editor.hasClass('expanded') ? 'Collapse' : 'Expand all');
                                      });
                
                                      printJSON();
                                      $('#editor').jsonEditor(json, { change: updateJSON, propertyclick: showPath });
                                  });
                
                        </script>

                    </div>

                </div>
                <div class="form-group">
                    Response
                    <textarea type="text" class="form-control" name="response">{{ $data->response }}</textarea>
                </div>
                <div class="form-group">
                    Failed
                    <textarea type="text" class="form-control" name="failed">{{ $data->failed }}</textarea>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            Response ID
                            <input type="text" class="form-control" name="response_id" value="{{ $data->response_id }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-block">Ubah</button>
                </div>
        </form>
    </div>
</section>



@endsection