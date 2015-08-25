    __DDI__.setPlugin('dragStateAsCursor');
    __DDI__.setPlugin('draggedElementTip');
    __DDI__.setPlugin('fixNoMouseSelect');
    __DDI__.setPlugin('lockCursorAsDefault');
    __DDI__.setPlugin('resizeIT');
    __DDI__.setPlugin('moveIT');

    /*
    *  create vertical bar to show where column will be moved
    */
    function createVerticalBar (el,height) {
        var vb = document.createElementExt('DIV',null,null,[['position','absolute'],
                                                            ['width','3px'],
                                                            ['height',height+'px'],
                                                            ['backgroundColor','none'],
                                                            ['display','none']]);
        var body = document.getElementsByTagName('BODY')[0];
        body.appendChild(vb);
        el.__MyVerticalBar = vb;
    }
    /*
    *  allow drop for header bar
    */
    function allowHeaderBarDragEnter(e) {
      if (e.__target.tagName.toLowerCase() != 'th') return false;
      var el = e.__dataTransfer.getData('domnode')
      if (el.tagName.toLowerCase() == 'th') e.__dataTransfer.dropEffect = 'move';
      else return false;
      if (!e.__currentTarget.__MyVerticalBar) {
        createVerticalBar(e.__currentTarget,e.__target.offsetHeight);
      }
      return true;
    }
    function allowHeaderBarDragOver(e) {
      var el = e.__dataTransfer.getData('domnode')
      if (el.tagName.toLowerCase() != 'th' || e.__target.innerHTML == 'Item') return false;
      if (el.tagName.toLowerCase() == 'th') e.__dataTransfer.dropEffect = 'move';
      else return;
      e.__currentTarget.__lastTarget = e.__target;
      if (!e.__currentTarget.__MyVerticalBar) createVerticalBar(e.__currentTarget,e.__target.offsetHeight);
      e.__currentTarget.__MyVerticalBar.style.display = 'block';
      e.__currentTarget.__MyVerticalBar.style.top = e.__targetOffsetY+'px';
      e.__currentTarget.__MyVerticalBar.style.left = e.__pageX<(e.__targetOffsetX+e.__target.offsetWidth/2)
                                                    ?(e.__targetOffsetX+'px')
                                                    :(e.__targetOffsetX+e.__target.offsetWidth+'px');
    }
    function allowHeaderBarDrop(e) {
      var el = e.__dataTransfer.getData('domnode')
      var lt = e.__currentTarget.__lastTarget;
      if (el != lt && lt.innerHTML != 'Item') {
        var lci = el.cellIndex;
        var target = e.__pageX<(lt.__offsetX+lt.offsetWidth/2)
                     ?(lt)
                     :(lt.cellIndex>=lt.parentNode.cells.length?lt:lt.parentNode.cells[lt.cellIndex+1])
        var cci = target!=null?target.cellIndex:lt.parentNode.cells.length;
        e.__currentTarget.insertBefore(el,target?target:null);
        var table = el.parentNode.parentNode.parentNode;
        var tr = table.getElementsByTagName('tr');
        var trL = tr.length;
        var tdL, ttd, td, tri;
        for (var i=0; i<trL; i++) {
          tri = tr[i];
          td = tri.getElementsByTagName('td');
          tdL = td.length;
          if (tdL == 0) continue;
          tri.insertBefore(tri.cells[lci],tri.cells[cci]?tri.cells[cci]:null);
        }
      }
      return false;
    }
    function allowHeaderBarDragEnd(e) {
      if (e.__currentTarget.__MyVerticalBar) {
        e.__currentTarget.__MyVerticalBar.removeNode(true);
        e.__currentTarget.__MyVerticalBar = null;
      }
    }
    var el = document.getElementById('zig_drag_and_drop');
    el.__onDragEnter = allowHeaderBarDragEnter;
    el.__onDragOver = allowHeaderBarDragOver;
    el.__onDrop = allowHeaderBarDrop;
    el.__onDragEnd = allowHeaderBarDragEnd;
    /*
    *  allow drag for heading
    */
    function handleHeadDragStart (e) {
      e.__dataTransfer.setData('domnode',e.__currentTarget);
      e.__dataTransfer.effectAllowed = 'move';
      __DDI__.disablePlugin('dragStateAsCursor');
      return true;
    }
    function handleHeadDragEnd (e) {
      __DDI__.enablePlugin('dragStateAsCursor');
    }
    /*
    *  allow resize for heading
    */
    function allowHeadResizeHoriz (e) {
      /*
      *  flag here that it's only drag
      */
      return {'x':true,'y':false}
    }
    function handleHeadResizeStart (e) {
      return true;
    }
    function handleHeadResize (e) {
      var ct = e.__currentTarget;
      var tbody = ct.parentNode.parentNode.parentNode.getElementsByTagName('TBODY')[0];
      var tr = tbody.rows;
      var trL = tr.length;
      for (var i=0; i<trL; i++) {
        tr[i].cells[ct.cellIndex].style.width = ct.style.width
      }
      return {'x' : { 'resize': true,
                      'min' : '1',
                      'max' : '10000'
                    },
              'y' : { 'resize' : false}
             }
    }
    var thead = document.getElementsByTagName('thead')[0];
    var th = thead.getElementsByTagName('th');
    var thL = th.length;
    for (var i = 0; i<thL; i++) {
      th[i].__onResizeBefore = allowHeadResizeHoriz;
      th[i].__onResize = handleHeadResize;
      th[i].__onResizeStart = handleHeadResizeStart;
      if ('Item' != th[i].innerHTML) {
        th[i].__onDragStart = handleHeadDragStart;
        th[i].__onDragEnd = handleHeadDragEnd;
      } else {
        th[i].__onDragStart = function (e) {
          if (e.__target.style.cursor != '')
            return true;
          else
            return false;
        }
      }
    }
    /*
    *  make table movable
    */
    function allowTableMoveStart (e) {
      /*
      *  Table could be moved using 'Item' bar only.
      */
      if (e.__target.innerHTML != 'Item') return false;
    }
    /*
    *  Checks constraits and tells moveIT plugin how it can move window
    */
    function allowTableMove (e) {
      var r = { 'x' : { 'move' : true,
                        'min' : null,
                        'max' : null
                      },
                'y' : { 'move' : true,
                        'min' : null,
                        'max' : null
                      }
              };
      return r;
    }
    var tbl = document.getElementsByTagName('table')[0];
    tbl.__onDragStart = function (e) {
      if (e.__target.innerHTML != 'Item') return false;
      e.__dataTransfer.effectAllowed = 'none';
      return true;
    }
    tbl.__onMoveStart = allowTableMoveStart;
    tbl.__onMove = allowTableMove;
