/*
   Class that defines a standard ratings component.
*/
// Messages for display above the individual ratings units. The length of the
// array also defines how many units to include in the component.
yg_Ratings.Msgs = new Array
(
   "差",
   "及格",
   "一般",
   "比较好",
   "棒极了"
);
yg_Ratings.Labels = new Array
(
   "1 Star",
   "2 Stars",
   "3 Stars",
   "4 Stars",
   "5 Stars"
);
// Path for all images.
//var path = "http://us.i1.yimg.com/us.yimg.com/i/us/ls/gr/";
//var path = "http://us.i1.yimg.com/us.yimg.com/i/us/sh/karma/yri_";
var path = "http://www.dphost.cn/images/yri_";
yg_Ratings.starbar = "star_";
// Image for set units.
yg_Ratings.UnitY = "yellow.gif";
// Image for set units <= the mouse over point.
yg_Ratings.UnitYMouseOver = "yellow.gif";
// Image for set units > the mouse over point.
yg_Ratings.UnitYMouseLess = "grey.gif";
// Image for unset units.
yg_Ratings.UnitN = "white.gif";
// Image for unset units <= the mouse over point.
yg_Ratings.UnitNMouseOver = "hover.gif";
yg_Ratings.DefaultMsg = " ";
function yg_Ratings(id, button, inputname, defaultval,type)
{
   // The id parameter is the name (a string) of the variable to which the
   // instance is assigned. (The variable is sent along to event handlers,
   // so it must be in the global scope.)
   var i, t;
   var attributes;
   var h1, h2;
   var d = document;
   var style;  
   this.starbar = type + "_";
   this.rating = 0;
   this.showbutton = button;
   if (defaultval < 1 || defaultval > 5) {
  defaultval = 0;
   }
   this.rating = defaultval;
 
   attributes = 'class="ygrtngs" id="' + id + '" style="' + style + '"';
   h1 = 'onMouseOut="return yg_Ratings_mouseOut(' + id + ');"';
   d.write('<span ' + attributes + ' ' + h1 + '>');
   if (defaultval > 0 && type == "star") {
       d.write('<div class="msg" id="'+id+'_msg">'+yg_Ratings.Msgs[defaultval-1]+'</div>');
   } else {
       d.write('<div class="msg" id="'+id+'_msg" style=/"display:none/">'+yg_Ratings.DefaultMsg+'</div>');
   }
   if (this.showbutton) {
      d.write('<span><strong>Rate it:</strong></span>');
   }
   for (i = 1; i <= yg_Ratings.Msgs.length; i++)
   {
      h1 = 'onMouseOver="return yg_Ratings_mouseOver(' + id + ', ' + i + ');"';
      h2 = 'onClick="return yg_Ratings_click(' + id + ', ' + i + ');"';
      d.write('<span class="unit "' + h1 + ' ' + h2 + '>');
      if (i <= defaultval) {
         d.write('<img src="' + path + this.starbar + yg_Ratings.UnitY + '" />');
      } else {
         d.write('<img src="' + path + this.starbar + yg_Ratings.UnitN + '" />');
      }
      d.write('</span>');
   }
   if(defaultval){
 d.write('<input type="hidden" name="'+inputname+'" id="input_'+id+'" value="' + defaultval + '" />');
   } else {
 d.write('<input type="hidden" name="'+inputname+'" id="input_'+id+'" />');   
   }
   this.input = document.getElementById('input_'+id);
   d.write('</span>');
 
   this.parent = document.getElementById(id);
   this.images = this.parent.getElementsByTagName("img");
   this.msg = document.getElementById(id + '_msg');
   this.id = id;
   var children = this.msg.childNodes;
   var node;
   for (var i = 0; i <  children.length; i++)
   {
      node = children[i];
      if (node.nodeType == 3)
      {
            this.DefaultMsg = node.nodeValue;
      }
   }
}
function yg_Ratings_set(n, oflag)
{
   if (arguments.length < 2)
      oflag = true;
  
   this.rating = n;
   this.DefaultMsg = yg_Ratings.Msgs[n-1];
//   if (this.showbutton) {
    this.showBtn("btnSave");
//   }
   this.update(n, oflag);
}
function yls_Ratings_showSubmit(sBtn) {
    var saveButton = document.getElementById(sBtn);
    if(saveButton != null) {
        saveButton.style.display="block";
    }
}
function yg_Ratings_setMsg(m)
{
   var children = this.msg.childNodes;
   var node;
   for (var i = 0; i < children.length; i++)
   {
      node = children[i];
      if (node.nodeType == 3)
      {
         // Using 0xA0 prevents the browser from collapsing empty messages.
            node.nodeValue = m;
      }
   }
}
function yg_Ratings_get()
{
   return this.rating;
}
function yg_Ratings_update(n, oflag)
{
   // The oflag parameter is true when the mouse is outside of the ratings
   // component. The n parameter is the
   if (this.starbar == 'star_') {
    if (oflag) {
    this.setMsg(this.DefaultMsg);
    } else {
    this.setMsg(yg_Ratings.Msgs[n - 1]);
    }
   }
   if (n == this.rating) {
      this.input.setAttribute("value", n);
   }
   for (i = 1; i <= yg_Ratings.Msgs.length; i++)
   {
      if (oflag)
      {
         if (i <= this.rating)
            this.images[i - 1].src = path + this.starbar + yg_Ratings.UnitY;
         else
            this.images[i - 1].src = path + this.starbar + yg_Ratings.UnitN;
      }
      else
      {
         if (i <= n)
         {
            if (i <= this.rating)
               this.images[i - 1].src = path + this.starbar + yg_Ratings.UnitYMouseOver;
            else
               this.images[i - 1].src = path + this.starbar + yg_Ratings.UnitNMouseOver;
         }
         else
         {
            if (i <= this.rating)
               this.images[i - 1].src = path + this.starbar + yg_Ratings.UnitYMouseLess;
            else
               this.images[i - 1].src = path + this.starbar + yg_Ratings.UnitN;
         }
      }
   }
   return true;
}
function yg_Ratings_click(obj, n)
{
   obj.set(n, false);
   return true;
}
function yg_Ratings_mouseOver(obj, n)
{
   obj.update(n, false);
   return true;
}
function yg_Ratings_mouseOut(obj)
{
   obj.update(0, true);
   return true;
}
yg_Ratings.prototype.set = yg_Ratings_set;
yg_Ratings.prototype.setMsg = yg_Ratings_setMsg;
yg_Ratings.prototype.get = yg_Ratings_get;
yg_Ratings.prototype.update = yg_Ratings_update;
yg_Ratings.prototype.showBtn = yls_Ratings_showSubmit;