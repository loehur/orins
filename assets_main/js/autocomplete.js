function autocomplete(inp, arr) {
  var currentFocus;
  inp.addEventListener("input", function (e) {
    var a,
      b,
      i,
      val = this.value;
    closeAllLists();
    if (!val) {
      return false;
    }
    currentFocus = -1;
    a = document.createElement("div");
    var wid = inp.getBoundingClientRect();
    a.style.width = wid.width;
    a.style.padding = wid.padding;
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("class", "autocomplete-items");
    this.parentNode.appendChild(a);

    var txtValue;
    var filter = inp.value.toUpperCase();
    var filterCount = filter.length;
    var chartMatch = [];

    for (key in arr) {
      txtValue = arr[key].nama.toUpperCase().trim();

      var match = false;
      var cekFind = [];
      var sortCek = 0;

      for (let k = 0; k < filter.length; k++) {
        cekFind[k] = 0;
        for (let j = sortCek; j < txtValue.length; j++) {
          if (filter.charAt(k) == txtValue.charAt(j)) {
            cekFind[k] = 1;
            chartMatch[j] = true;
            sortCek = j + 1;
            break;
          }
        }

        if (cekFind[k] == 0) {
          match = false;
          break;
        } else {
          const sumFind = cekFind.reduce((partialSum, a) => partialSum + a, 0);
          if (sumFind == filterCount) {
            match = true;
            break;
          }
        }
      }

      if (filter.length == 0) {
        closeAllLists();
      } else {
        if (match == true) {
          b = document.createElement("div");
          b.setAttribute("data-value", arr[key].id);

          var textInject = "";
          var allMatch = true;
          for (let j = 0; j < txtValue.length; j++) {
            if (chartMatch[j] == true) {
              textInject += "<strong>" + txtValue.charAt(j) + "</strong>";
            } else {
              allMatch = false;
              textInject += txtValue.charAt(j);
            }
          }

          b.innerHTML = textInject.trim();
          console.log(filter.length + " " + txtValue.length);
          if (allMatch == true && filter.length == txtValue.length) {
            this.setAttribute("data-value", arr[key].id);
          } else {
            this.setAttribute("data-value", "");
          }

          b.innerHTML += "<input type='hidden' value='" + arr[key].nama + "'>";
          b.addEventListener("click", function (e) {
            inp.value = this.getElementsByTagName("input")[0].value.trim();
            inp.setAttribute("data-value", this.getAttribute("data-value"));
            closeAllLists();
          });
          a.appendChild(b);
        } else {
          this.setAttribute("data-value", "");
        }
      }
    }
  });

  inp.addEventListener("keydown", function (e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
      currentFocus++;
      addActive(x);
    } else if (e.keyCode == 38) {
      currentFocus--;
      addActive(x);
    } else if (e.keyCode == 13) {
      e.preventDefault();
      if (currentFocus == -1) {
        currentFocus = 0;
      }
      if (currentFocus > -1) {
        try {
          if (x[currentFocus]) {
            if (x) choose(x);
          }
        } catch (error) {}
      }
    }
  });

  function choose(x) {
    x[currentFocus].click();
  }

  function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (x[currentFocus]) {
      if (currentFocus >= x.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = x.length - 1;
      x[currentFocus].classList.add("autocomplete-active");
    }
  }

  function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }

  function closeAllLists(elmnt) {
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }

  document.addEventListener("click", function (e) {
    closeAllLists(e.target);
  });
}
