let url = location.host;//so it works locally and online
$("#add_drug").submit(function(event){//on a submit event on the element with id add_drug
    alert($("#name").val() + " sent successfully!");//alert this in the browser
})



$("#update_drug").submit(function(event){// on clicking submit
    event.preventDefault();//prevent default submit behaviour

    //var unindexed_array = $("#update_drug");
    var unindexed_array = $(this).serializeArray();//grab data from form
    var data = {}

    $.map(unindexed_array, function(n, i){//assign keys and values from form data
        data[n['name']] = n['value']
    })


    var request = {//use a put API request to use data from above to replace what's on database
        "url" : `http://${url}/api/drugs/${data.id}`,
        "method" : "PUT",
        "data" : data
    }

    $.ajax(request).done(function(response){
        alert(data.name + " Updated Successfully!");
		window.location.href = "/";//redirects to index after alert is closed
    })

})

if(window.location.pathname == "/"){
    $ondelete = $("table tbody td a.delete");
    $ondelete.click(function(){
        let id = $(this).attr("data-id")

        let request = {
            "url" : `http://${url}/api/drugs/${id}`,
            "method" : "DELETE"
        }

        if(confirm("Do you really want to delete this drug?")){
            $.ajax(request).done(function(response){
                alert("Drug deleted Successfully!");
                location.reload();
            })
        }

    })
}