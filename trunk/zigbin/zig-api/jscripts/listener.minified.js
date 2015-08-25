
function zig_listener(element,type,expression,bubbling)
{if(window.addEventListener){element.addEventListener(type,expression,bubbling);return true;}else if(window.attachEvent){element.attachEvent('on'+type,expression);return true;}else return false;}