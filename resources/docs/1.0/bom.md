# BillOfMaterial

---

- [BillOfMaterial](#bill_of_material)
  
<a name="bill_of_material"></a>
## Receive BillOfMaterial
- ##### **Method** 
  ```bash
  POST
  ```
- ##### **Headers**
```bash
Content-Type   : application/json
Accept         : application/json
Authorization  : -
```
- ##### **Request**
  ```bash
  http://localhost:8000/api/netsuite/receive-bom
  ```
- ##### **Data**
  ```bash
  {
    "record_type": "purchasing",
    "data": [
        {
            "bom": {
                "internal_id_bom": "1821",
                "bom_name": "Tes Assembly 1",
                "internal_subsidiary_id": "1",
                "subsidiary": "HOLDING : TRADING : MPP",
                "memo": null,
                "item": []
            }
        },
        {
            "bom": {
                "internal_id_bom": "5034",
                "bom_name": "CGL - LIVEBIRD - AYAM KARKAS",
                "internal_subsidiary_id": "6",
                "subsidiary": "HOLDING : RPA : CGL",
                "memo": "WO-1",
                "item": [
                    {
                        "internal_id_item": "1824",
                        "type": "Finished Goods",
                        "sku": "1100000001",
                        "name": "AYAM KARKAS BROILER (RM)",
                        "qty": 1,
                        "unit": "7"
                    },
                    {
                        "internal_id_item": "1998",
                        "type": "By Product",
                        "sku": "1211810004",
                        "name": "HATI AMPELA KOTOR BROILER",
                        "qty": 0.05,
                        "unit": "7"
                    },
                    {
                        "internal_id_item": "2012",
                        "type": "By Product",
                        "sku": "1211840002",
                        "name": "KEPALA LEHER BROILER",
                        "qty": 0.05,
                        "unit": "7"
                    },
                    {
                        "internal_id_item": "2008",
                        "type": "By Product",
                        "sku": "1211830001",
                        "name": "KAKI KOTOR BROILER",
                        "qty": 0.05,
                        "unit": "7"
                    },
                    {
                        "internal_id_item": "2006",
                        "type": "By Product",
                        "sku": "1211820005",
                        "name": "USUS BROILER",
                        "qty": 0.05,
                        "unit": "7"
                    }
                ]
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
    "message": "Save BOM success",
    "apps_id": 46,
    "data": [
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "1821",
            "apps_id": "2"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5034",
            "apps_id": "3"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5035",
            "apps_id": "4"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5036",
            "apps_id": "5"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5037",
            "apps_id": "6"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5038",
            "apps_id": "7"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5039",
            "apps_id": "8"
        },
        {
            "code": "1",
            "activity": "Updated",
            "status": "Success",
            "message": "Save BOM success",
            "internal_id_bom": "5040",
            "apps_id": "9"
        }
    ]
}

```