# Purchasing

---

- [Purchasing](#purchasing)
  
<a name="purchasing"></a>
## Receive Purchasing
- ##### **Method** 
  ```bash
  POST
  ```
- ##### **Headers**
```bash
Content-Type   : application/json
Accept         : application/json
Authorization  : Bearer LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C.....XXXXXX
```
- ##### **Request**
  ```bash
  http://localhost:8000/api/netsuite/receive-po
  ```
- ##### **Data**
  ```bash
  {
   "record_type":"purchasing",
   "data":[
      {
         "data_purchasing":{
            "document_number":"PO.CGL.2021.06.00001",
            "type_po":"PO LB",
            "vendor":null,
            "vendor_name":"SEMEN INDONESIA BETON",
            "tipe_ekspedisi":"Other",
            "internal_id_po":"473449",
            "subsidiary":"CGL"
         },
         "data_vendor":{
            "internal_id_vendor":null,
            "nama_vendor":"SEMEN INDONESIA BETON",
            "alamat":"PT SEMEN INDONESIA (PERSERO)\nl. Banjar Sari II No.12, RT.2\/RW.8, Cilandak Bar., Kec. Cilandak\nJakarta selatan 12430\nIndonesia",
            "no_telp":null,
            "jenis_ekspedisi":"Other",
            "subsidiary":"CGL",
            "wilayah_vendor":"Jabodetabek"
         },
         "data_item":[
               {
                    "internal_id_item":"744",
                    "sku":"11110211",
                    "name":"AYAM KARKAS 12-13",
                    "category_item":null,
                    "subsidiary":"CGL",
                    "jenis_ayam":null,
                    "jumlah_do":null,
                    "tanggal_kirim":"26-Jun-2021",
                    "internal_id":"744",
                    "item":"AYAM KARKAS 12-13",
                    "rate":"30886.00",
                    "ukuran_ayam":null,
                    "qty":"1",
                    "qty_pcs":"1"
                },
                {
                    "internal_id_item":"744",
                    "sku":"11110211",
                    "name":"AYAM KARKAS 12-13",
                    "category_item":null,
                    "subsidiary":"CGL",
                    "jenis_ayam":null,
                    "jumlah_do":null,
                    "tanggal_kirim":"26-Jun-2021",
                    "internal_id":"744",
                    "item":"AYAM KARKAS 12-13",
                    "rate":"30886.00",
                    "ukuran_ayam":null,
                    "qty":"1",
                    "qty_pcs":"1"
                }
         ]
      }
   ]
}

        
  ```
- ##### **Response**
  ```bash
   {
    "code": "1",
    "status": "Success",
    "message": "Save PO success",
    "apps_id": 33,
    "data": [
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473449"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473452"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473503"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473505"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473516"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473518"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473525"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save PO success",
            "apps_id": null,
            "internal_id_po": "473747"
        }
    ]
}

  ```