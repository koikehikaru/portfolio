$(function () {
  $(".fade-img li:not(:first-child)").hide();
  setInterval(function () {
    $(".fade-img li:first-child")
      .fadeOut("slow")
      .next("li")
      .fadeIn("slow")
      .end()
      .appendTo(".fade-img");
  }, 3500);
});

$(function () {
  $(window).scroll(function () {
    $(".effect-fade").each(function () {
      var elemPos = $(this).offset().top;
      var scroll = $(window).scrollTop();
      var windowHeight = $(window).height();
      if (scroll > elemPos - windowHeight) {
        $(this).addClass("effect-scroll");
      }
    });
  });
});
