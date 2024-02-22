<div class="row">
    <div class="col-6 pr-1">
        <section class="panel">
            <div class="card-body p-2">
                <div class="font-weight-bold mb-2">DATA PERIODE {{ Carbon\Carbon::parse($tanggal)->subDay(14)->format('Y-m-d'). ' - ' . Carbon\Carbon::parse($tanggal)->subDay(7)->format('Y-m-d') }}</div>
                <div class="border rounded p-1 mb-3">
                    <small>Rendemen Total </small>
                    <div class="font-weight-bold">{{ number_format($result['last_rendemen']->rendemen_total, 2) }} %</div>
                    <div class="proj-progress-card">
                        <div class="">
                            <div class="progress thin-bar">
                                <div class="progress-bar progress-bar-default bg-default" style="width:{{ number_format($result['last_rendemen']->rendemen_total, 2) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Rendemen Tangkap </small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->rendemen_tangkap, 2) }} %</div>
                            <div class="proj-progress-card">
                                <div class="">
                                    <div class="progress thin-bar">
                                        <div class="progress-bar progress-bar-info bg-info"
                                            style="width:{{ number_format($result['last_rendemen']->rendemen_tangkap, 2) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Rendemen Kirim </small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->rendemen_kirim, 2) }} %</div>
                            <div class="proj-progress-card">
                                <div class="">
                                    <div class="progress thin-bar">
                                        <div class="progress-bar progress-bar-success bg-success"
                                            style="width:{{ number_format($result['last_rendemen']->rendemen_kirim, 2) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="font-weight-bold mb-1">Informasi Selesai Potong</div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Berat RPA</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->berat_rpa, 2) }}</div>
                        </div>
                    </div>
                    <div class="col px-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Berat Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->berat_grading, 2) }}</div>
                        </div>
                    </div>
                    <div class="col px-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Berat Evis</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->berat_evis, 2) }}</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Darah Bulu</small>
                            <div class="font-weight-bold">
                                {{ number_format($result['last_rendemen']->darah_bulu, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 mt-3">
                    <div class="col pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekor RPA</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->ekor_rpa) }}</div>
                        </div>
                    </div>
                    <div class="col px-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekor Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->ekor_grading) }}</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Selisih Ekor</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->selisih_ekor) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-6 pr-1">
                        <div class="mb-1"><b>Informasi DO</b></div>
                        <div class="border rounded p-1 mb-2">
                            <small>Ekor DO</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->ekor_do) }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Berat DO</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->berat_do, 2) }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Rerata DO</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->rerata_do, 2) }}</div>
                        </div>
                    </div>


                    <div class="col-lg-8 col-6 pl-1">
                        <div class="mb-1"><b>Informasi Terima LB</b></div>
                        <div class="row">
                            <div class="col-6 pr-1">
                                <div class="border rounded p-1 mb-2">
                                    <small>Ekoran Seckel</small>
                                    <div class="font-weight-bold">{{ number_format($result['last_rendemen']->ekoran_seckel) ?? 0 }}
                                    </div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Kg Terima</small>
                                    <div class="font-weight-bold">
                                        {{ number_format($result['last_rendemen']->kg_terima, 2) ?? 0 }}</div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Rerata Terima LB</small>
                                    <div class="font-weight-bold">
                                        {{ number_format($result['last_rendemen']->rata_terima_lb, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-6 pl-1">
                                <div class="border rounded p-1 mb-2">
                                    <small>Susut Tangkap</small>
                                    <div class="font-weight-bold">{{ number_format($result['last_rendemen']->susut_tangkap, 2) }} %</div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Susut Kirim</small>
                                    <div class="font-weight-bold">{{ number_format($result['last_rendemen']->susut_kirim, 2) }} %</div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Selisih Seckel</small>
                                    <div class="font-weight-bold">{{ number_format($result['last_rendemen']->susut_seckel) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2 mb-1"><b>Informasi Grading</b></div>
                <div class="row">
                    <div class="col-6 pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekoran Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->ekoran_grading) ?? 0 }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Berat Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->berat_grading, 2) ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6 pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Selisih Seckel-Grading</small>
                            <div class="font-weight-bold">
                                {{ number_format($result['last_rendemen']->selisih_seckel_grading) }}
                            </div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Rerata Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['last_rendemen']->rerata_grading, 2) }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
    <div class="col-6 pl-1">
        <section class="panel">
            <div class="card-body p-2">
                <div class="font-weight-bold mb-2">DATA PERIODE {{ Carbon\Carbon::parse($tanggal)->subDay(6)->format('Y-m-d'). ' - ' . $tanggal }}</div>
                <div class="border rounded p-1 mb-3">
                    <small>Rendemen Total </small>
                    <div class="font-weight-bold">{{ number_format($result['now_rendemen']->rendemen_total, 2) }} %</div>
                    <div class="proj-progress-card">
                        <div class="">
                            <div class="progress thin-bar">
                                <div class="progress-bar progress-bar-default bg-default" style="width:{{ number_format($result['now_rendemen']->rendemen_total, 2) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Rendemen Tangkap </small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->rendemen_tangkap, 2) }} %</div>
                            <div class="proj-progress-card">
                                <div class="">
                                    <div class="progress thin-bar">
                                        <div class="progress-bar progress-bar-info bg-info"
                                            style="width:{{ number_format($result['now_rendemen']->rendemen_tangkap, 2) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Rendemen Kirim </small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->rendemen_kirim, 2) }} %</div>
                            <div class="proj-progress-card">
                                <div class="">
                                    <div class="progress thin-bar">
                                        <div class="progress-bar progress-bar-success bg-success"
                                            style="width:{{ number_format($result['now_rendemen']->rendemen_kirim, 2) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="font-weight-bold mb-1">Informasi Selesai Potong</div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Berat RPA</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->berat_rpa, 2) }}</div>
                        </div>
                    </div>
                    <div class="col px-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Berat Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->berat_grading, 2) }}</div>
                        </div>
                    </div>
                    <div class="col px-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Berat Evis</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->berat_evis, 2) }}</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Darah Bulu</small>
                            <div class="font-weight-bold">
                                {{ number_format($result['now_rendemen']->darah_bulu, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 mt-3">
                    <div class="col pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekor RPA</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->ekor_rpa) }}</div>
                        </div>
                    </div>
                    <div class="col px-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekor Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->ekor_grading) }}</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Selisih Ekor</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->selisih_ekor) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-6 pr-1">
                        <div class="mb-1"><b>Informasi DO</b></div>
                        <div class="border rounded p-1 mb-2">
                            <small>Ekor DO</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->ekor_do) }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Berat DO</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->berat_do, 2) }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Rerata DO</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->rerata_do, 2) }}</div>
                        </div>
                    </div>


                    <div class="col-lg-8 col-6 pl-1">
                        <div class="mb-1"><b>Informasi Terima LB</b></div>
                        <div class="row">
                            <div class="col-6 pr-1">
                                <div class="border rounded p-1 mb-2">
                                    <small>Ekoran Seckel</small>
                                    <div class="font-weight-bold">{{ number_format($result['now_rendemen']->ekoran_seckel) ?? 0 }}
                                    </div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Kg Terima</small>
                                    <div class="font-weight-bold">
                                        {{ number_format($result['now_rendemen']->kg_terima, 2) ?? 0 }}</div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Rerata Terima LB</small>
                                    <div class="font-weight-bold">
                                        {{ number_format($result['now_rendemen']->rata_terima_lb, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-6 pl-1">
                                <div class="border rounded p-1 mb-2">
                                    <small>Susut Tangkap</small>
                                    <div class="font-weight-bold">{{ number_format($result['now_rendemen']->susut_tangkap, 2) }} %</div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Susut Kirim</small>
                                    <div class="font-weight-bold">{{ number_format($result['now_rendemen']->susut_kirim, 2) }} %</div>
                                </div>
                                <div class="border rounded p-1 mb-2">
                                    <small>Selisih Seckel</small>
                                    <div class="font-weight-bold">{{ number_format($result['now_rendemen']->susut_seckel) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2 mb-1"><b>Informasi Grading</b></div>
                <div class="row">
                    <div class="col-6 pr-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Ekoran Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->ekoran_grading) ?? 0 }}</div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Berat Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->berat_grading, 2) ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-6 pl-1">
                        <div class="border rounded p-1 mb-2">
                            <small>Selisih Seckel-Grading</small>
                            <div class="font-weight-bold">
                                {{ number_format($result['now_rendemen']->selisih_seckel_grading) }}
                            </div>
                        </div>
                        <div class="border rounded p-1 mb-2">
                            <small>Rerata Grading</small>
                            <div class="font-weight-bold">{{ number_format($result['now_rendemen']->rerata_grading, 2) }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>
