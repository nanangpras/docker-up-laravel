# SalesOrder

---

- [SalesOrder](#sales_order)
  
<a name="sales_order"></a>
## Receive SalesOrder
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
  http://localhost:8000/api/netsuite/receive-so
  ```
- ##### **Data**
  ```bash
{
	"record_type": "sales_order",
	"data": [
		{
			"data_customer": {
				"internal_id_customer": "62438",
				"nama_customer": "LION SUPERINDO. PT - JATI KRAMAT",
				"category_customer": "Modern Market",
				"id_sales": "",
				"sales": "",
				"subsidiary": "CGL",
				"internal_id_parent": "61858"
			},
			"data_sales_order": {
				"internal_id_so": "485941",
				"nomor_so": "SO.CGL.2021.07.00025",
				"nomor_po": "",
				"internal_id_customer": "62438",
				"nama_customer": "LION SUPERINDO. PT - JATI KRAMAT",
				"tanggal_kirim": "30-Jul-2021",
				"tanggal_so": "30-Jul-2021",
				"customer_partner": "",
				"alamat_customer_partner": "",
				"wilayah": "",
				"id_sales": "",
				"sales": "",
				"memo": "",
				"sales_channel": "Modern Market",
				"alamat_ship_to": "PT LION SUPERINDO - JATI KRAMAT\nJL. JATI KRAMAT\nBEKASI  \nIndonesia",
				"internal_subsidiary_id": "6",
				"subsidiary": "CGL"
			},
			"data_item": [
				{
					"internal_id_item": "1922",
					"sku": "1211400012",
					"name": "AYAM PARTING BROILER 14-15",
					"category_item": "Parting",
					"subsidiary": "CGL",
					"description_item": "AYAM PARTING BROILER 14-15",
					"part": "",
					"qty": "100",
					"unit": "Kilogram",
					"rate": "30000.00",
					"qty_pcs": "100",
					"harga_per_pcs": "",
					"bumbu": "",
					"plastik": "",
					"memo": ""
				},
				{
					"internal_id_item": "1920",
					"sku": "1211400010",
					"name": "AYAM PARTING BROILER 12-13",
					"category_item": "Parting",
					"subsidiary": "CGL",
					"description_item": "AYAM PARTING BROILER 12-13",
					"part": "",
					"qty": "500",
					"unit": "Kilogram",
					"rate": "31000.00",
					"qty_pcs": "500",
					"harga_per_pcs": "",
					"bumbu": "",
					"plastik": "",
					"memo": ""
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
    "message": "Save SO success",
    "apps_id": 38,
    "data": [
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": null,
            "internal_id_so": "55503"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 1,
            "internal_id_so": "55503"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 1,
            "internal_id_so": "55503"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 1,
            "internal_id_so": "55503"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": null,
            "internal_id_so": "55504"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 2,
            "internal_id_so": "55504"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 2,
            "internal_id_so": "55504"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 2,
            "internal_id_so": "55504"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": null,
            "internal_id_so": "55505"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 3,
            "internal_id_so": "55505"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 3,
            "internal_id_so": "55505"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 3,
            "internal_id_so": "55505"
        },
        {
            "code": "1",
            "activity": "Insert",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": null,
            "internal_id_so": "55506"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 4,
            "internal_id_so": "55506"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 4,
            "internal_id_so": "55506"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save SO success",
            "apps_id": 4,
            "internal_id_so": "55506"
        }
    ]
}

```