
var map = {

  'list':function()
  {
    var result = {};
    $.ajax({ url: path+"map/list.json", dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },

  'set':function(id, fields)
  {
    var result = {};
    $.ajax({ url: path+"map/set.json", data: "id="+id+"&fields="+JSON.stringify(fields), async: false, success: function(data){} });
    return result;
  },

  'remove':function(id)
  {
    $.ajax({ url: path+"map/delete.json", data: "id="+id, async: false, success: function(data){} });
  }

}

