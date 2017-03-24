/*
 Sticky-kit v1.1.2 | WTFPL | Leaf Corcoran 2015 | http://leafo.net
*/
// (function(){var b,f;b=this.jQuery||window.jQuery;f=b(window);b.fn.stick_in_parent=function(d){var A,w,J,n,B,K,p,q,k,E,t;null==d&&(d={});t=d.sticky_class;B=d.inner_scrolling;E=d.recalc_every;k=d.parent;q=d.offset_top;p=d.spacer;w=d.bottoming;null==q&&(q=0);null==k&&(k=void 0);null==B&&(B=!0);null==t&&(t="is_stuck");A=b(document);null==w&&(w=!0);J=function(a,d,n,C,F,u,r,G){var v,H,m,D,I,c,g,x,y,z,h,l;if(!a.data("sticky_kit")){a.data("sticky_kit",!0);I=A.height();g=a.parent();null!=k&&(g=g.closest(k));
// if(!g.length)throw"failed to find stick parent";v=m=!1;(h=null!=p?p&&a.closest(p):b("<div />"))&&h.css("position",a.css("position"));x=function(){var c,f,e;if(!G&&(I=A.height(),c=parseInt(g.css("border-top-width"),10),f=parseInt(g.css("padding-top"),10),d=parseInt(g.css("padding-bottom"),10),n=g.offset().top+c+f,C=g.height(),m&&(v=m=!1,null==p&&(a.insertAfter(h),h.detach()),a.css({position:"",top:"",width:"",bottom:""}).removeClass(t),e=!0),F=a.offset().top-(parseInt(a.css("margin-top"),10)||0)-q,
// u=a.outerHeight(!0),r=a.css("float"),h&&h.css({width:a.outerWidth(!0),height:u,display:a.css("display"),"vertical-align":a.css("vertical-align"),"float":r}),e))return l()};x();if(u!==C)return D=void 0,c=q,z=E,l=function(){var b,l,e,k;if(!G&&(e=!1,null!=z&&(--z,0>=z&&(z=E,x(),e=!0)),e||A.height()===I||x(),e=f.scrollTop(),null!=D&&(l=e-D),D=e,m?(w&&(k=e+u+c>C+n,v&&!k&&(v=!1,a.css({position:"fixed",bottom:"",top:c}).trigger("sticky_kit:unbottom"))),e<F&&(m=!1,c=q,null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),
// h.detach()),b={position:"",width:"",top:""},a.css(b).removeClass(t).trigger("sticky_kit:unstick")),B&&(b=f.height(),u+q>b&&!v&&(c-=l,c=Math.max(b-u,c),c=Math.min(q,c),m&&a.css({top:c+"px"})))):e>F&&(m=!0,b={position:"fixed",top:c},b.width="border-box"===a.css("box-sizing")?a.outerWidth()+"px":a.width()+"px",a.css(b).addClass(t),null==p&&(a.after(h),"left"!==r&&"right"!==r||h.append(a)),a.trigger("sticky_kit:stick")),m&&w&&(null==k&&(k=e+u+c>C+n),!v&&k)))return v=!0,"static"===g.css("position")&&g.css({position:"relative"}),
// a.css({position:"absolute",bottom:d,top:"auto"}).trigger("sticky_kit:bottom")},y=function(){x();return l()},H=function(){G=!0;f.off("touchmove",l);f.off("scroll",l);f.off("resize",y);b(document.body).off("sticky_kit:recalc",y);a.off("sticky_kit:detach",H);a.removeData("sticky_kit");a.css({position:"",bottom:"",top:"",width:""});g.position("position","");if(m)return null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.remove()),a.removeClass(t)},f.on("touchmove",l),f.on("scroll",l),f.on("resize",
// y),b(document.body).on("sticky_kit:recalc",y),a.on("sticky_kit:detach",H),setTimeout(l,0)}};n=0;for(K=this.length;n<K;n++)d=this[n],J(b(d));return this}}).call(this);


// 	$('.menu').stick_in_parent();

(function(){
var a = document.querySelector('.menu'), b = null, K = null, Z = 0, P = 0, N = 0;  // если у P ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента, если у N — нижний край дойдёт до нижнего края элемента. Может быть отрицательным числом
window.addEventListener('scroll', Ascroll, false);
document.body.addEventListener('scroll', Ascroll, false);
function Ascroll() {
  var Ra = a.getBoundingClientRect(),
      R1bottom = document.querySelector('.content').getBoundingClientRect().bottom;
  if (Ra.bottom < R1bottom) {
    if (b == null) {
      var Sa = getComputedStyle(a, ''), s = '';
      for (var i = 0; i < Sa.length; i++) {
        if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
          s += Sa[i] + ': ' +Sa.getPropertyValue(Sa[i]) + '; '
        }
      }
      b = document.createElement('div');
      b.className = "stop";
      b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
      a.insertBefore(b, a.firstChild);
      var l = a.childNodes.length;
      for (var i = 1; i < l; i++) {
        b.appendChild(a.childNodes[1]);
      }
      a.style.height = b.getBoundingClientRect().height + 'px';
      a.style.padding = '0';
      a.style.border = '0';
    }
    var Rb = b.getBoundingClientRect(),
        Rh = Ra.top + Rb.height,
        W = document.documentElement.clientHeight,
        R1 = Math.round(Rh - R1bottom),
        R2 = Math.round(Rh - W);
    if (Rb.height > W) {
      if (Ra.top < K) {  // скролл вниз
        if (R2 + N > R1) {  // не дойти до низа
          if (Rb.bottom - W + N <= 0) {  // подцепиться
            b.className = 'sticky';
            b.style.top = W - Rb.height - N + 'px';
            Z = N + Ra.top + Rb.height - W;
          } else {
            b.className = 'stop';
            b.style.top = - Z + 'px';
          }
        } else {
          b.className = 'stop';
          b.style.top = - R1 +'px';
          Z = R1;
        }
      } else {  // скролл вверх
        if (Ra.top - P < 0) {  // не дойти до верха
          if (Rb.top - P >= 0) {  // подцепиться
            b.className = 'sticky';
            b.style.top = P + 'px';
            Z = Ra.top - P;
          } else {
            b.className = 'stop';
            b.style.top = - Z + 'px';
          }
        } else {
          b.className = '';
          b.style.top = '';
          Z = 0;
        }
      }
      K = Ra.top;
    } else {
      if ((Ra.top - P) <= 0) {
        if ((Ra.top - P) <= R1) {
          b.className = 'stop';
          b.style.top = - R1 +'px';
        } else {
          b.className = 'sticky';
          b.style.top = P + 'px';
        }
      } else {
        b.className = '';
        b.style.top = '';
      }
    }
    window.addEventListener('resize', function() {
      a.children[0].style.width = getComputedStyle(a, '').width
    }, false);
  }
}
})();

$('.arrow').click(function() {
	var arrow = $(this);
	var open = arrow.hasClass('arrow--open');
	var close = arrow.hasClass('arrow--close');

	if (!open && !close) {
		arrow.css('left','93px');
		$('.menu__wrapper').show();
		$('.content').css('width','calc(100% - 98px)');
		setTimeout(function() {
			$('.menu__wrapper').css({'left':'0px'});
		})

		setTimeout(function() {
			arrow.addClass('arrow--open');
		},900)
	}
	if (open){
		arrow.css('left','0px');
		$('.menu__wrapper').css({'left':'-98px'});
		$('.content').css('width','100%');
		setTimeout(function() {
			$('.menu__wrapper').hide();
			arrow.removeClass('arrow--open');
			arrow.addClass('arrow--close');
		},900)
	}
	if (close){

		arrow.removeClass('arrow--close').css('left','93px');
		$('.menu__wrapper').show();
		$('.content').css('width','calc(100% - 98px)');

		setTimeout(function() {
			$('.menu__wrapper').css({'left':'0px'});
		})

		setTimeout(function() {
			arrow.addClass('arrow--open');
		},900)
	}
});

