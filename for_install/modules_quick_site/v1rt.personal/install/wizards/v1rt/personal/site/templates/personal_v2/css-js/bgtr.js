bgtr = function() {
 tables = document.getElementsByTagName("table");
  for (i = 0; i < tables.length; i++) {
        if (tables[i].className == "table-1") {
          tr = document.getElementsByTagName("tr");
          for (j = 0; j < tr.length; j++) {
           if (j%2) tr[j].className = "odd";
          }
        }
  }
}       
window.onload = bgtr;