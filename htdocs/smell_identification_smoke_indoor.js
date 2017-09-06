window.onload = function () {

  var count = 0;

  document.getElementsByName('fire_away')[0].onclick = function() {

    var e = document.getElementsByName("smoke_indoors_someone")[0];
    var ee = e.options[e.selectedIndex].value;

    var f = document.getElementsByName("smoke_indoors_past")[0];
    var ff = f.options[f.selectedIndex].value;

    var g = document.getElementsByName("smoke_indoors_work")[0];
    var gg = g.options[g.selectedIndex].value;

    var h = document.getElementsByName("smoke_indoors_years")[0];
    var hh = h.value;

    var i = document.getElementsByName("smoke_indoors_years_status")[0];
    var ii = i.options[i.selectedIndex].value;

    if ((ee == 'yes' || ff == 'yes' || gg == 'yes') && hh == '' && ii == '') {
      count++;
      const element = h;
      const elementRect = element.getBoundingClientRect();
      const absoluteElementTop = elementRect.top + window.pageYOffset;
      const middle = absoluteElementTop - (window.innerHeight / 2);
      window.scrollTo(0, middle);

      if (count === 1) {
        $('input[name=smoke_indoors_years]').after('<p><font color="red">Please answer this question.</font></p>');
      }

      return false;
    }

  };

}
