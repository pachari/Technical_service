function initRepairGet() {
    var o = {
        get: function() {
            return "id=" + $E("id").value + "&" + this.name + "=" + this.value;
        },
        onSuccess: function() {
            customer_name.valid();

        },
        onChanged: function() {
            customer_name.reset();

        }
    };
    /*---------------------------Moomai---------------------- */
    var t = {
        get: function() {
            return "customer_id=" + $E("customer_id").value + "&" + this.name + "=" + this.value;

        },
        onSuccess: function() {
            contact_name.valid();
        },
        onChanged: function() {
            contact_name.reset();
        }
    };
    var d = {
        get: function() {
            return "id=" + $E("id").value + "&" + this.name + "=" + this.value;
        },
        onSuccess: function() {
            approve.valid();
        },
        onChanged: function() {
            approve.reset();
        }
    };
    var approve = initAutoComplete(
        "approve_name",
        WEB_URL + "index.php/repair/model/autocomplete/find3",
        "approve_name,employee",
        "find3",
        d
    );
    var customer_name = initAutoComplete(
        "customer_name",
        WEB_URL + "index.php/repair/model/autocomplete/find",
        "customer_name,address",
        "find",
        o
    );
    var contact_name = initAutoComplete(
        "contact_name",
        WEB_URL + "index.php/repair/model/autocomplete/find2",
        "contact_name,contact_tel",
        "find2",
        t
    );
    /*var type_work = initAutoComplete(
    "type_work",
    WEB_URL + "index.php/repair/model/autocomplete/find",
        "type_work",
        "find",
        o
);*/
}