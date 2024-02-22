# Location

---

- [Location](#location)
  
<a name="sales_order"></a>
## Receive Location
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
  http://localhost:8000/api/netsuite/receive-location
  ```
- ##### **Data**
  ```bash
{
    "record_type": "location",
    "data": [
        {
            "data_location": {
                "nama_location": "CGL NONE",
                "internal_id_location": "16",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "EBA NONE",
                "internal_id_location": "17",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Storage Live Bird",
                "internal_id_location": "118",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Chiller Bahan Baku",
                "internal_id_location": "119",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Storage Produksi (WIP)",
                "internal_id_location": "120",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Chiller Finished Good",
                "internal_id_location": "121",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Storage Expedisi",
                "internal_id_location": "122",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Storage ABF",
                "internal_id_location": "123",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        },
        {
            "data_location": {
                "nama_location": "CGL - Cold Storage 1",
                "internal_id_location": "124",
                "kategori_gudang" : "Production/Warehouse/Others",
                "subsidiary_id" : "6",
                "subsidiary_name" : "CGL",
                "isinactive" : "1/0"
            }
        }
    ]
}
  ```
- ##### **Response**
```bash
{
    "code": "1",
    "status": "Success",
    "message": "Save Location success",
    "apps_id": 31,
    "data": [
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 64,
            "internal_id_location": "16"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 65,
            "internal_id_location": "17"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 66,
            "internal_id_location": "118"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 67,
            "internal_id_location": "119"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 68,
            "internal_id_location": "120"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 69,
            "internal_id_location": "121"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 70,
            "internal_id_location": "122"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 71,
            "internal_id_location": "123"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 72,
            "internal_id_location": "124"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 73,
            "internal_id_location": "125"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 74,
            "internal_id_location": "126"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 75,
            "internal_id_location": "127"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 76,
            "internal_id_location": "128"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 77,
            "internal_id_location": "129"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 78,
            "internal_id_location": "130"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 79,
            "internal_id_location": "131"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 80,
            "internal_id_location": "132"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 81,
            "internal_id_location": "133"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 82,
            "internal_id_location": "134"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 83,
            "internal_id_location": "135"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save Location success",
            "apps_id": 84,
            "internal_id_location": "218"
        }
    ]
}

```