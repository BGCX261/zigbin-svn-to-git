if (Node && !Node.prototype) {
  var node = document.createTextNode('');
  var Node = node.constructor;
}
if (window.Node) {
    Node.prototype.removeNode = function(removeChildren) {
    var self = this;
    if (Boolean(removeChildren))
      return this.parentNode.removeChild( self );
    else {
      var r=document.createRange();
      r.selectNodeContents(self);
      return this.parentNode.replaceChild(r.extractContents(),self);
    }
  }
  Node.prototype.swapNode = function(swapNode) {
    var self = this;
    n = self.cloneNode(true);
    nt = swapNode.cloneNode(true);
    self.parentNode.insertBefore(nt,self);
    self.removeNode(true);
    swapNode.parentNode.insertBefore(n,swapNode);
    swapNode.removeNode(true);
  }
  if (!Node.prototype.attachEvent)
    Node.prototype.attachEvent = function (e,f,c) {
      var self = this;
      return self.addEventListener(e.substr(2), f, false); // was true--Opera7b workaround!
    }
  if (!Node.prototype.fireEvent)
    Node.prototype.fireEvent = function (e) {
      var eventTypes = { resize : ['HTMLEvents',1,0],
                       scroll : ['HTMLEvents',1,0],
                       focusin : ['HTMLEvents',0,0],
                       focusout : ['HTMLEvents',0,0],
                       gainselection : ['HTMLEvents',1,0],
                       loseselection : ['HTMLEvents',1,0],
                       activate : ['HTMLEvents',1,1],
                       //events above should be UIEvents, but Mozilla seems does not support this type
                       //or refuses to create such event from inside JS
                       load : ['HTMLEvents',0,0],
                       unload : ['HTMLEvents',0,0],
                       abort : ['HTMLEvents',1,0],
                       error : ['HTMLEvents',1,0],
                       select : ['HTMLEvents',1,0],
                       change : ['HTMLEvents',1,0],
                       submit : ['HTMLEvents',1,1],
                       reset : ['HTMLEvents',1,0],
                       focus : ['HTMLEvents',0,0],
                       blur : ['HTMLEvents',0,0],
                       click : ['MouseEvents',1,1],
                       mousedown : ['MouseEvents',1,1],
                       mouseup : ['MouseEvents',1,1],
                       mouseover : ['MouseEvents',1,1],
                       mousemove : ['MouseEvents',1,0],
                       mouseout : ['MouseEvents',1,0],
                       keypress : ['KeyEvents',1,1],
                       keydown : ['KeyEvents',1,1],
                       keyup : ['KeyEvents',1,1],
                       DOMSubtreeModified : ['MutationEvents',1,0],
                       DOMNodeInserted : ['MutationEvents',1,0],
                       DOMNodeRemoved : ['MutationEvents',1,0],
                       DOMNodeRemovedFromDocument : ['MutationEvents',0,0],
                       DOMNodeInsertedIntoDocument : ['MutationEvents',0,0],
                       DOMAttrModified : ['MutationEvents',1,0],
                       DOMCharacterDataModified : ['MutationEvents',1,0]
                     };
      var self = this;
      e = e.substr(2);
      if (!eventTypes[e]) return false;
      var evt = document.createEvent(eventTypes[e][0]);
      evt.initEvent(e,eventTypes[e][1],eventTypes[e][2]);
      return self.dispatchEvent(evt);
    }
}
if (!window.attachEvent) {
  window.attachEvent= function (e,f,c) {
    var self = this;
    if (self.addEventListener) return self.addEventListener(e.substr(2), f, false); // was true--Opera7b workaround!
    else self[e] = f;                                                               // thing for Opera 6......
  }
}

(__DDI__ = {
  /*
  *  Object that implements Drag'n'Drop interface
  *  It is based on MS dataTransfer interface
  */
  __dataTransfer__ : function (el) {
    var self = this;
    /*
    *  Stores textual info, if any
    *
    *  @type string
    *  @access private
    */
    var __text__ = null;
    /*
    *  Stores HTML info, if any
    *
    *  @type string
    *  @access private
    */
    var __html__ = null;
    /*
    *  Stores DOM tree, if any
    *
    *  @type object
    *  @access private
    */
    var __dom__ = null;
    /*
    *  Stores currently dragging node
    *
    *  @type object
    *  @access private
    */
    var __element__ = el;
    
    /*
    *  Public properties
    *
    *  effectAllowed
    *  dropEffect
    */
    var __________________________Publics__________________________;
    /*
    *  Property specifying allowed effect from caller
    *
    *  @type string
    *  @access public
    */
    this.effectAllowed = 0;
    var __effectAllowed = null;
    /*
    *  Property specifying allowed drop actions
    *
    *  @type string
    *  @access public
    */
    this.dropEffect = null;
    var __dropEffect = null;
    
    /*
    *  Public methods
    *
    *  setData
    *  getData
    *  clearData
    *  getDragState
    *  getClonedElement
    */
    
    /*
    *  Used to set more info on dragged element
    *
    *  @param string value type
    *  @param mixed value
    *  @return boolean operation state
    *  @access public
    */
    this.setData = function (type,obj) {
      switch (type.toLowerCase()) {
        case "text" : 
          __text__ = obj;
          break;
        case "domnode" :
          __dom__ = obj;
          break;
        default:
          return false;
      }
      return true;
    }
    /*
    *  Used to retrieve info on dragged element
    *
    *  @param string value type
    *  @return mixed value or null
    *  @access public
    */
    this.getData = function (type) {
      switch (type.toLowerCase()) {
        case "text" : 
          return __text__;
        case "domnode" :
          return __dom__;
        default:
          return null;
      }
    }
    /*
    *  Used to clear some info on dragged element
    *
    *  @param string value type
    *  @access public
    */
    this.clearData = function () {
      switch (arguments[0].toLowerCase()) {
        case "text" : 
          __text__ = null;
          break;
        case "domnode" :
          __dom__ = null;
          break;
        case "" :
          __text__ = null;
          __dom__ = null;
          break;
      }
    }
    /*
    *  Used by plugins to retrieve drag operation state
    *
    *  Return values
    *  0 - drop not allowed
    *  1 - copy allowed
    *  2 - move allowed
    *  4 - link allowed
    *
    *  @return int state
    *  @access public
    */
    this.getDragState = function () {
      return Number(__dropEffect & __effectAllowed);
    }

    /*
    *  Used by plugins to retrieve clone of dragged element
    *
    *  @return object element clone
    *  @access public
    */
    this.getClonedElement = function () {
      var el = __element__.cloneNode(true);
      el.id = '';
      return el;
    }
    
    var __________________________Protected__________________________;
    /*
    *  Here and below are interfaces for:
    *  onDragStart
    *  onDrag
    *  onDragEnd
    *  onDragEnter
    *  onDragOver
    *  onDragLeave
    *  onDrop
    */
    
    /*
    *  Handles onDragStart
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDragStart = function (evt,el) {
      var res = executeHandler(evt,__element__,'__onDragStart',__element__);
      if (res !== false) applyEffectAllowed();
      return res;
    }
    /*
    *  Handles onDrag
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDrag = function (evt,el) {
      var res = executeHandler(evt,__element__,'__onDrag',__element__);
      if (res !== false) applyEffectAllowed();
      return res;
    }
    /*
    *  Handles onDragEnd
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDragEnd = function (evt,el) { if(el!=__element__) return executeHandler(evt,el?el:__element__,'__onDragEnd',el?el:__element__);}
    /*
    *  Handles onDragEnter
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDragEnter = function (evt,el) { 
      /*
      *  execute handler only if draggded node wants to be dropped
      */
      if (__effectAllowed > 0)
        return executeHandler(evt,el,'__onDragEnter',el);
      else
        return false;
    }
    
    /*
    *  Handles onDragOver
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDragOver = function (evt,el) {
      var res = false;
      /*
      *  execute handler only if draggded node wants to be dropped
      */
      if (__effectAllowed > 0) {
        res = executeHandler (evt,el,'__onDragOver',el);
        if (res !== false) {
          /*
          *  Setting dropEffect bitmask 
          */
          switch (String(self.dropEffect).toLowerCase()) {
            case 'copy' : __dropEffect = 1;
                          break;
            case 'move' : __dropEffect = 2;
                          break;
            case 'link' : __dropEffect = 4;
                          break;
            default :     __dropEffect = 0;
          }
        }
      }
      return res;
    }
    
    /*
    *  Handles onDragLeave
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDragLeave = function (evt,el) {
      var res = false;
      /*
      *  execute handler only if draggded node wants to be dropped
      */
      if (__effectAllowed > 0) {
        res = executeHandler (evt,el,'__onDragLeave',el);
        /*
        *  Reset dropEffect
        */
        self.dropEffect = 'none';
        __dropEffect = 0;
      }
      return res;
    }
    
    /*
    *  Handles desired drop method
    *
    *  @param object event
    *  @param object DOM node
    *  @access protected
    */
    this.__handleDrop = function (evt,el) {
      var res = false;
      /*
      *  execute handler only if draggded node wants to be dropped
      *  and receiving wants accept it
      */
      if (__effectAllowed & __dropEffect) {
        if (!el.__onDrop) return false;
        res = executeHandler (evt,el,'__onDrop',el);
        /*
        *  We will continue only if __onDrop handler will return 'true'
        */
        if (res !== false) {
          switch (__dropEffect & __effectAllowed) {
            case 1 : 
              el.appendChild(__element__.cloneNode(true));
              break;
            case 2 :
                try {
                 el.appendChild(__element__);
                } 
                 catch (e) {
                 alert('Node cannot be moved here');
                }
              break;
          }
        }
      }
      return res;
    }
    var __________________________Privates__________________________;

    /*
    *  Executes handler.
    *
    *  @param object event
    *  @param object element to call handler
    *  @param string handler hame
    *  @param object target element
    *  @return mixed false or handler execution result
    *  @access private
    */
    var executeHandler = function (evt,el,h,target) {
      /*
      *  if element has no such handler
      */
      if (!el || !el[h]) return false;
      /*
      *  setting current target
      */
      evt.__currentTarget = target;
      /*
      *  calculate mouse coords over target
      */
      __DDI__.geometry.recalcCurrentTarget(evt);
      /*
      *  execute handler
      */
      return el[h](evt);
    }
    /*
    *  Converts literal effectAllowed to bitmask EffectAllowed
    *
    *  @access private
    */
    var applyEffectAllowed = function () {
      /*
      *  Applying effectAllowed bitmask
      */
      switch (String(self.effectAllowed).toLowerCase()) {
        case 'none' : __effectAllowed = 0;
                      break;
        case 'copy' : __effectAllowed = 1;
                      break;
        case 'move' : __effectAllowed = 2;
                      break;
        case 'link' : __effectAllowed = 4;
                      break;
        case 'copymove' : __effectAllowed = 3;
                          break;
        case 'copylink' : __effectAllowed = 5;
                          break;
        case 'movelink' : __effectAllowed = 6;
                          break;
        case 'all' : __effectAllowed = 7;
                     break;
      }
    }
  },

  /*
  *  Use to add/remove and execute plugins
  *
  *  Adding plugin to scope
  *  either:
  *  __DDI__.plugin.<plugin_name> = new function() { } (create new function object)
  *  or
  *  __DDI__.plugin.<plugin_name> = { } (simple object)
  *
  *  How to add plugin to execution queue:
  *  Call either __DDI__.pluginManager.setPlugin or __DDI__.setPlugin with parameter "<plugin_name>"
  *  Ex. __DDI__.setPlugin("fixMouseSelect");
  *
  *  How to remove plugin from execution queue:
  *  Call either __DDI__.pluginManager.removePlugin or __DDI__.removePlugin with parameter "<plugin_name>"
  *  Ex. __DDI__.removePlugin("fixNoMouseSelect");
  *
  */
  pluginManager : new (function () {
    var self = this;
    /*
    *  Stores plugins execution state
    *  true - plugin is available to execute
    *  false - plugin cannot be executed
    *
    *  @type Object
    *  @access private
    */
    var pluginState = {};
    /*
    *  Stores flags of forced plugin execution
    *
    *  @type Object
    *  @access private
    */
    var forcedPlugin = {};
    /*
    *  defines plugin groups:
    *  - core plugins, checks/enables/disables browser features
    *  - system plugins, implements some additional drag functionality like node moving
    *  - visual plugins, producing some visual effects
    *
    *  @var object
    *  @access private
    */
    var pluginGroups = {'DDI_CORE' : 1,
                        'DDI_SYSTEM' : 2,
                        'DDI_VISUAL' : 4
                       }
    /*
    *  stores plugin groups execution state
    *  true means allowed to be executed
    *
    *  @var array
    *  @access private
    */
    var pluginGroupState = {};
    for (var i in pluginGroups) {
      if (Object.prototype[i]) continue;
      pluginGroupState[i] = true;
    }
    /*
    *  Stores everything about plugins
    *
    *  @type Object
    *  @access private
    */
    var plugins =  {'runtime' : {'always' : {}
                                },
                    'dragstart' : { 'before' : {},
                                    'after' : {}
                                  },
                    'drag' : { 'before' : {},
                               'after' : {}
                             },
                    'dragend' : { 'before' : {},
                                  'after' : {}
                             }
                   }
    /*
    *  function sort plugin methods in the ascending order, 
    *  as described in pluginGroups
    *
    *  @param object methods to sort
    *  @return sorted methods
    *  @access private
    */
    var doPluginMethodSort = function (methods) {
      var tmp = [];
      /*
      *  put all methods in the array
      */
      for (var pm in methods) {
        if (Object.prototype[pm]) continue;
        tmp[tmp.length] = pm;
      }
      /*
      *   sort array depending on pluginGroups
      */
      tmp.sort(function(a,b){var pa = __DDI__.plugin[a]; 
                             var pb = __DDI__.plugin[b]; 
                             var pag = pluginGroups[pa.group];
                             var pbg = pluginGroups[pb.group];
                             if (pag<pbg) return -1;
                             if (pag>pbg) return 1;
                             return 0
                            });
      var tmpL = tmp.length;
      ret = {};
      /*
      *  fill return object in sorted order
      */
      for (var i=0; i<tmpL; i++) {
        ret[tmp[i]] = methods[tmp[i]];
      }
      /*
      *  free up memory
      */
      return ret;
    }
    var __________________________Public__________________________;
    /*
    *  Adds plugin in the scope
    *
    *  @param string event
    *  @param string position (before or after)
    *  @param string plugin name
    *  @return boolean operation state
    *  @access public
    */
    this.setPlugin = function(name) {
      var evt, phase;
      var plg = __DDI__.plugin[name];
      var res = false;
      /*
      *  make a check for each of event and phase
      */
      for (evt in plugins) {
        if (Object.prototype[evt]) continue;
        if (plg && plg[evt])
          for (phase in plugins[evt]) {
            if (Object.prototype[phase]) continue;
            if (!plugins[evt][phase][name] && plg[evt][phase] && typeof(plg[evt][phase]) == 'function') {
              /*
              *  adding plugin only if it exists, it is funciton, and was not installed before
              */
              plugins[evt][phase][name] = plg[evt][phase];
              /*
              *  by default plugin is ready to execute
              */
              pluginState[name] = true;
              /*
              *  do plugin methods sorting
              */
              plugins[evt][phase] = doPluginMethodSort(plugins[evt][phase]);
              res = true;
            }
          }
      }
      /*
      *  If plugin needs to be initialized - do it
      */
      if (res && plg.init) plg.init();
      if (res) {
        plg.enablePluginGroup = enablePluginGroup;
        plg.disablePluginGroup = disablePluginGroup;
        plg.forcePlugin = forcePlugin;
        plg.unforcePlugin = unforcePlugin;
      }
      return res;
    }
    /*
    *  Removes plugin from the scope
    *
    *  @param string event
    *  @param string position (before or after)
    *  @param string plugin name
    *  @return boolean operation state
    *  @access public
    */
    this.removePlugin = function(name) {
      var res = false;
      if (!__DDI__.plugin[name]) return false;
      /*
      *  make a check for each of event and phase
      */
      for (evt in plugins) {
        if (Object.prototype[evt]) continue;
        for (phase in plugins[evt]) {
          if (Object.prototype[phase]) continue;
          if (plugins[evt][phase][name]) {
            /*
            *  delete only real plugins
            */
            delete(plugins[evt][phase][name]);
            delete(pluginState[name]);
            delete(forcedPlugin[name]);
            res = true;
          }
        }
      }
      if (res && __DDI__.plugin[name]['shutdown']) __DDI__.plugin[name]['shutdown']();
      return res;
    }
    /*
    *  Disables plugin
    *
    *  @param string plugin name
    *  @access public
    */
    this.disablePlugin = function (name) {
      pluginState[name] = false;
    }
    /*
    *  Enables plugin
    *
    *  @param string plugin name
    *  @access public
    */
    this.enablePlugin = function (name) {
      pluginState[name] = true;
    }

    var __________________________Protected__________________________;

    /*
    *  Executes plugins in specified queue and phase
    *
    *  @param stirng event name, one of 'runtime', 'dragstart', 'drag', 'dragend'
    *  @param string event phase, one of 'before', 'after', 'always' (for 'runtime' plugins only)
    *  @param object event object
    *  @access public (should not be called outside DDI)
    */
    this.__call = function (evt,pos,event) {
      var pl;
      var res = true;
      if (plugins[evt] && plugins[evt][pos])
        for (pl in plugins[evt][pos]) {
          if (Object.prototype[pl]) continue;
          var g = __DDI__.plugin[pl].group;
          if (pluginState[pl] && pluginGroupState[g] || forcedPlugin[pl]) res = res&plugins[evt][pos][pl](event);
        }
      return res;
    }

    /*
    *  Enables plugin group execution
    *
    *  @param string plugin group
    *  @access protected
    */
    var enablePluginGroup = function (g) {
      if (pluginGroups[this.group]<pluginGroups[g]) pluginGroupState[g] = true;
    }
    /*
    *  Disables plugin group execution
    *
    *  @param string plugin group
    *  @access protected
    */
    var disablePluginGroup = function (g) {
      if (pluginGroups[this.group]<pluginGroups[g]) pluginGroupState[g] = false;
    }
    /*
    *  Forces plugin execution
    *
    *  @param string plugin name
    *  @access protected
    */
    var forcePlugin = function (name) {
      forcedPlugin[name] = true;
    }
    /*
    *  Disables forced plugin execution
    *
    *  @param string plugin name
    *  @access protected
    */
    var unforcePlugin = function (name) {
      delete(forcedPlugin[name]);
    }

  })(),
  geometry : new (function () {
    var self = this;
    var __________________________Public__________________________;
    /*
    *  pageX and pageY properties, mouse pointer position in document
    *
    *  @type integer
    *  @access public
    */
    this.pageX = null;
    this.pageY = null;
    /*
    *  pageDx and pageDy properties, difference between the current and last mouse position
    *
    *  @type integer
    *  @access public
    */
    this.dX;
    this.dY;

    /*
    *  mouse pointer coordinates, relative to event.__currentTarget node
    *
    *  @type integer
    *  @access public
    */
    this.currentTargetOffsetX;
    this.currentTargetOffsetY;

    /*
    *  mouse pointer coordinates, relative to event.__target node
    *
    *  @type integer
    *  @access public
    */
    this.targetOffsetX;
    this.targetOffsetY;

    /*
    *  Calculates some of geometry things
    *
    *  @param object where to apply recalculations, optional
    *  @access public
    */
    this.recalc = function (e) {
      /*
      *  Save previous coordinates
      */
      self.dX = self.pageX;
      self.dY = self.pageY;
      /*
      *  Finiding new cursor position
      */
      var c = document.getElementsByTagName((document.compatMode&&document.compatMode=="CSS1Compat")?"html":"body")[0];
      self.pageX = window.event?window.event.clientX+c.scrollLeft:e.pageX;
      self.pageY = window.event?window.event.clientY+c.scrollTop:e.pageY;
      
      /*
      *  Calculating difference
      */
      self.dX = self.dX==null?0:(self.pageX-self.dX);
      self.dY = self.dY==null?0:(self.pageY-self.dY);

      self.recalcCurrentTarget(e);
      self.recalcTarget(e);

      if (e!=null) self.apply (e);
    };
    /*
    *  Applies geometry calculations to the object
    *
    *  @param object event
    *  @param array properties to apply
    *  @access public
    */
    this.apply = function (e,p) {
      /*
      *  Setting cross-browser geometry properties on the object
      */
      if (p == null) {
        for (var i in self) if (!Object[i] && typeof self[i] != 'function') e['__'+i] = self[i]
      } else {
        var pL = p.length;
        for (var i=0;i<pL;i++) e['__'+p[i]] = parseInt(self[p[i]])
      }
    }
    /*
    *  Calculates offsets of element
    *
    *  @param object DOM node
    *  @return object X and Y offsets
    *  @access public
    */
    this.calcOffset = function(el) {
      var x=0,y=0;
      while (el) {
       x+=el.offsetLeft;
       y+=el.offsetTop;
       el = el.offsetParent;
      }
      return {'x':x,'y':y};
    }
    /*
    *  recalculates mouse coordinates over event.__target
    *
    *  @param object event object
    *  @return object x,y offset
    *  @access public
    */
    this.recalcTarget = function (e) {
      var off = recalcLayer (e, e.__target,'targetOffsetX','targetOffsetY');
      this.apply(e,['targetOffsetX','targetOffsetY']);
      return off;
    }
    /*
    *  recalculates mouse coordinates over event.__currentTarget
    *
    *  @param object event object
    *  @return object x,y offset
    *  @access public
    */
    this.recalcCurrentTarget = function (e) {
      var off = recalcLayer (e, e.__currentTarget,'currentTargetOffsetX','currentTargetOffsetY');
      this.apply(e,['currentTargetOffsetX','currentTargetOffsetY']);
      return off;
    }
    var __________________________Private__________________________;
    /*
    *  recalculates specified node offset and saves result in properties
    *
    *  @param object event object
    *  @param object target node
    *  @param object X propery
    *  @param object Y propery
    *  @return object x,y offset
    *  @access private
    */
    var recalcLayer = function (e, el, propX, propY) {
      /*
      *  save current position
      */
      var off = {'x' : 0, 'y' : 0}
      if (el) {
        off = self.calcOffset(el);
        /*
        *  apply properties as requested
        */
        self[propX] = off.x;
        e[propX] = off.x;
        el.__offsetX = off.x;
        e[propY] = off.y;
        self[propY] = off.y;
        el.__offsetY = off.y;
        /*
        *  apply correct mouse positions to element
        */
        el.__layerX = (e.clientX||e.x)-off.x;
        el.__layerY = (e.clientY||e.y)-off.y;
      }
      return off;
    }
  })(),
  /*
  *  Object to store user-defined plugins.
  */
  plugin : {},
  /*
  *  Flag showing the drag state
  */
  beforeDrag : false,
  /*
  *  Current node, mouse pointer flies over
  */
  currentTarget : false,
  /*
  *  Object that do dragging
  */
  dataTransfer : null,

  /*
  *  Binds to mousedown event on Document object 
  *  initializes dataTransfer, when triggered
  *
  */
  initDrag : function (evt) {
//    var evt = window.event || e;
    var el = evt.target || evt.srcElement;
    /*
    *  Set event target
    */
    evt.__target = el;
    /*
    *  recalculate all geometry
    */
    __DDI__.geometry.recalc(evt);
    /*
    *  Pointing to the target....
    */
    __DDI__.beforeDrag = el;
    /*
    *  check nodes from top one with __onDragStart handler
    */
    while (__DDI__.beforeDrag) {
      /*
      *  initialize library only if it has onDragStart handler
      */
      if (__DDI__.beforeDrag.__onDragStart) {
        /*
        *  Assign the current target
        */
        __DDI__.currentTarget = __DDI__.beforeDrag;
        /*
        *  set actual drag element
        */
        evt.__currentTarget = __DDI__.currentTarget;
        /*
        *  calculate mouse coords over target
        */
        __DDI__.geometry.recalcCurrentTarget(evt);
        /*
        *  create new dataTransfer object
        */
        __DDI__.dataTransfer = new __DDI__.__dataTransfer__(evt.__currentTarget);
        /*
        *  Apply __dataTransfer object to current event
        *  "__" (2 underscore signs) prefix is used to avoid conflicts with MSIE's dataTransfer object
        */
        evt.__dataTransfer = __DDI__.dataTransfer;
        /*
        *  Handling __onDragStart
        *  Handler may (and should) setup desired value to 'effectAllowed' property
        *  Continue if allowed
        */
        if (false !== __DDI__.dataTransfer.__handleDragStart(evt,el)) break;
        /*
        *  if __onDragStart returns false, it's considered as "do not drag me"
        */
        delete (__DDI__.dataTransfer);
      }
      /*
      *  and get parent node to check in next round
      */
      __DDI__.beforeDrag = __DDI__.beforeDrag.parentNode;
    }
    /*
    *  if we still have no node to start dragging... return
    */
    if (!__DDI__.currentTarget || __DDI__.currentTarget.parentNode == null) {
      delete (__DDI__.dataTransfer);
      __DDI__.currentTarget = null;
      return;
    }
    /*
    * Call plugins "dragStart.after"
    */
    __DDI__.pluginManager.__call('dragstart','after',evt);
  },
  /*
  *  Binds to mousemove event and continuously checks elements under mouse pointer,
  *  when we drag nothing
  */
  mouseMover : function (evt) {
//    var evt = window.event || e;
    var el = evt.srcElement || evt.target;
    /*
    *  Applying element target to event
    */
    evt.__target = el;
    /*
    *  Recalculate geometry stuff and apply it to the event
    */
    __DDI__.geometry.recalc(evt);
    /*
    *  call continuously running plugin method
    */
    __DDI__.pluginManager.__call('runtime','always',evt);
    /*
    *  Continue only if we have something to drag
    */
    if (!__DDI__.dataTransfer) return;
    /*
    *  Apply __dataTransfer object to current event
    */
    evt.__dataTransfer = __DDI__.dataTransfer;
    /*
    * Applying current target to the event
    */
    evt.__currentTarget = __DDI__.currentTarget;
    /*
    *  calculate mouse coords over target
    */
    __DDI__.geometry.recalcCurrentTarget(evt);
    /*
    *  Call plugins "drag.before"
    *  and continue execution only if all plugins agree (returned true or undefined)
    */
    if (__DDI__.pluginManager.__call('drag','before',evt)) {
      /*
      *  check each node for __onDragEnter handler
      *  node is not current
      */
      while (__DDI__.currentTarget !== el && 
             /*
             *  and has no __onDragEnter or has __onDragEnter and it returns false
             */
             (!el.__onDragEnter || el.__onDragEnter && 
              (evt.__currentTarget = el, __DDI__.dataTransfer.__handleDragEnter(evt,el))
             ) &&
             /*
             *  and after __onDragEnter it still has no __onDragOver
             */
             !el.__onDragOver && (el = el.parentNode)) {/* do nothing */ }
      /*
      *  If node is not the same as before
      */
      if (el && __DDI__.currentTarget != el) {
        /*
        *  Handle onDragLeave
        */
        __DDI__.dataTransfer.__handleDragLeave(evt,__DDI__.currentTarget);
        /*
        *  Assigning new element to currentTarget property
        */
        __DDI__.currentTarget = el;
      }
      /*
      * Applying current target to the event
      */
      evt.__currentTarget = __DDI__.currentTarget;
      /*
      *  calculate mouse coords over target
      */
      __DDI__.geometry.recalcCurrentTarget(evt);
      /*
      *  Handling onDrag, if allowed
      */
      __DDI__.dataTransfer.__handleDrag (evt,el);
      /*
      *  Handling onDragOver
      */
      __DDI__.dataTransfer.__handleDragOver (evt,el);
    }
    /*
    * Call plugins "drag.after"
    */
    __DDI__.pluginManager.__call('drag','after',evt);
  },
  /*
  *  Remove reference to dragged object and clean up everything
  */
  stopDrag : function (evt) {
    /*
    *  Drop the flag
    */
    __DDI__.beforeDrag = false;
    /*
    *  Clean up properties and call handlers only if we drag something
    */
    if (__DDI__.dataTransfer) {

//    var evt = window.event || e;
      var el = evt.srcElement || evt.target;
  
      /*
      *  Applying element target to event
      */
      evt.__target = el;
      /*
      *  recalculate all geometry
      */
      __DDI__.geometry.recalc(evt);
      /*
      *  Applying __dataTransfer object to current event
      */
      evt.__dataTransfer = __DDI__.dataTransfer;
  
      /*
      * Call plugins "dragEnd.before"
      */
      __DDI__.pluginManager.__call('dragend','before',evt);
      /*
      *  Handle __onDrop 
      */
      __DDI__.dataTransfer.__handleDrop(evt,__DDI__.currentTarget);
      /*
      *  Handle __onDragEnd
      */
      __DDI__.dataTransfer.__handleDragEnd(evt);
      __DDI__.dataTransfer.__handleDragEnd(evt,__DDI__.currentTarget);
      /*
      *  Free up memory from old dataTransfer object
      */
      delete (__DDI__.dataTransfer);
      __DDI__.currentTarget = null;
    }
    /*
    * Call plugins "dragEnd.after"
    */
    __DDI__.pluginManager.__call('dragend','after',evt);    
  },
  /*
  *  Wrapper to __DDI__.pluginManager.setPlugin
  */
  setPlugin : function (name) {
    return __DDI__.pluginManager.setPlugin(name);
  },
  /*
  *  Wrapper to __DDI__.pluginManager.removePlugin
  */
  removePlugin : function (name) {
    return __DDI__.pluginManager.removePlugin(name);
  },
  /*
  *  Wrapper to __DDI__.pluginManager.enablePlugin
  */
  enablePlugin : function (name) {
    return __DDI__.pluginManager.enablePlugin(name);
  },
  /*
  *  Wrapper to __DDI__.pluginManager.disablePlugin
  */
  disablePlugin : function (name) {
    return __DDI__.pluginManager.disablePlugin(name);
  },
  init : function () {
    document.attachEvent('onmousemove',__DDI__.mouseMover);
    document.attachEvent('onmousedown',__DDI__.initDrag);
    document.attachEvent('onmouseup',__DDI__.stopDrag);
  }
}).init();

/*
*  Creates element all-at-once
*
*  @param string tag name
*  @param string class name(s)
*  @param mixed array of event handlers as [['event','handler_name'], ['event1','handler_name1'], ....]
*  @param mixed array of style attributes as [['prop','style_desc'], ['prop1','style_desc1'], ....]
*  @param mixed array of child nodes
*  @param mixed array of additional properties
*  @return object DOM tree
*  @access public
*/
if (!document.createElementExt) {
  document.createElementExt = function (tag,c,h,s,ch,p) {
    var L, i;
    var el = document.createElement(tag);
    if (!el) return false;
    if(c) { el.setAttribute('className',c); el.setAttribute('class',c);}
    if(h) { L = h.length; for (i=0; i<L; i++) el.attachEvent(h[i][0],h[i][1]);}
    if(s) { L = s.length; for (i=0; i<L; i++) el.style[s[i][0]] = s[i][1];}
    if(ch) { L = ch.length; for (i=0; i<L; i++) el.appendChild(ch[i]);}
    if(p) { L = p.length; for (i=0; i<L; i++) el[p[i][0]] = p[i][1];}
    return el;
  }
}

__DDI__.plugin.dragStateAsCursor = new function () {
  var self = this;
  this.name = 'Show drag state';
  this.description = 'Plugin is used to show drag state near mouse pointer';
  this.group = 'DDI_VISUAL';
  var el = null;
  var ________________________Publics______________________;
  this.opacity = 50;
  /*
  *  Tooltip X coordinate, relative to cursor
  *
  *  @type integer
  *  @access public
  */
  this.t_x = 10;
  /*
  *  Tooltip X coordinate, relative to cursor
  *
  *  @type integer
  *  @access public
  */
  this.t_y = 2;
  /*
  *  Private methods.
  *  Do not call them directly
  */
  this.init = function () {
    el = document.createElementExt("DIV", null, null,
                                   [['position','absolute'],
                                    ['border', '1px solid black'],
                                    ['display', 'none'],
                                    ['zIndex', '9999']
                                   ],
                                   [document.createTextNode('')]
                                  );
    var body = document.getElementsByTagName('body')[0];
    body.appendChild(el);
  }
  this.drag = {
    'after' : function (e) {
      switch (e.__dataTransfer.getDragState()) {
        case 1 : 
          el.firstChild.nodeValue = 'copy-allowed';
          break;
        case 2 : 
          el.firstChild.nodeValue = 'move-allowed';
          break;
        case 4 : 
          el.firstChild.nodeValue = 'link-allowed';
          break;
        case 0 : 
        default:
          el.firstChild.nodeValue = 'no-drop';
      }
      el.style.top = e.__pageY+self.t_y+'px';
      el.style.left = e.__pageX+self.t_x+'px';
      el.style.display = 'block';
    }
  }
  this.dragend = {
    'after' : function (e) {
      if (el) el.style.display = 'none';
    }
  }
  this.shutdown = function() {
    if (el != null) {
      el.removeNode(true);
      el = null;
    }
  }
};
__DDI__.plugin.draggedElementTip = new function () {
  var self = this;
  var el = null;
  this.name = 'Dragged element tooltip';
  this.description = 'Plugin shows transparent copy of dragged element near mouse pointer';
  this.group = 'DDI_VISUAL';
  var ________________________Publics______________________;
  /*
  *  Opacity percentage
  *
  *  @type integer
  *  @access public
  */
  this.opacity = 75;
  /*
  *  Tooltip X coordinate, relative to cursor
  *
  *  @type integer
  *  @access public
  */
  this.t_x = 10;
  /*
  *  Tooltip X coordinate, relative to cursor
  *
  *  @type integer
  *  @access public
  */
  this.t_y = 18;
  /*
  *  Tooltip width
  *
  *  @type integer
  *  @access public
  */
  this.t_w = 250;
  /*
  *  Tooltip height
  *
  *  @type integer
  *  @access public
  */
  this.t_h = 75;
  /*
  *  Private methods.
  *  Do not call them directly
  */
  this.dragstart = {
    'after' : function (e) {
      var el1, el2;
      var tel = e.__dataTransfer.getClonedElement();
      switch (tel.tagName.toLowerCase()) {
        case "th":
        case "td":
          el1 = 'div';
          el2 = 'table';
          tel = document.createElementExt('tbody',null,null,null,[document.createElementExt('tr',null,null,null,[tel])]);
          break;
        case "tr":
          tel = document.createElementExt('tbody',null,null,null,[tel]);
        case "thead":
        case "tbody":
        case "tfoot":
          el1 = 'div';
          el2 = 'table';
          break;
        default:
          el1 = el2 = 'div';
      }
      el = document.createElementExt(el1,null,null,[['-moz-opacity',self.opacity/100],
                                                    ['opacity',self.opacity/100],
                                                    ['-khtml-opacity',self.opacity/100],
                                                    ['filter','Alpha(Opacity='+self.opacity+')'],
                                                    ['position','absolute'],
                                                    ['width',self.t_w+'px'],
                                                    ['height',self.t_h+'px'],
                                                    ['overflow','hidden'],
                                                    ['zIndex',9999],
                                                    ['display','none']],
                                                    [document.createElementExt(el2,null,null,[['position','relative']],
                                                                               [tel])]);
      el.firstChild.firstChild.style.margin = 0;
      el.firstChild.firstChild.style.padding = 0;
      el.firstChild.firstChild.style.top = 0;
      el.firstChild.firstChild.style.left = 0;
      var body = document.getElementsByTagName('body')[0];
//      body.appendChild(el);
    }
  }
  this.drag = {
    'before' : function (e) {
      if (el) {
        el.style.left = e.__pageX+self.t_x+'px';
        el.style.top = e.__pageY+self.t_y+'px';
        el.style.display = 'block'
      }
      return true;
    }
  }
  this.dragend = {
    'after' : function (e) {
      if (el != null) {
        el.style.display = 'none';
        el.removeNode(false);
        el = null;
      }
    }
  }
}
__DDI__.plugin.fixNoMouseSelect = new function () {
  this.name = 'No mouse select';
  this.description = 'Plugin is used to disable mouse selection during drag';
  this.group = 'DDI_CORE';
  var el = false;
  var reset = false;
  this.init = function () {
    if (!el) {
      el = document.createElementExt('INPUT', null, null,
                                     [['width',0],
                                      ['height',0],
                                      ['border',0],
                                      ['padding',0],
                                      ['margin',0],
                                      ['background','transparent'],
                                      ['font-size','0']
                                     ], null, 
                                     [['disabled','disabled'],['id','mouse_select_input_fixx']]);

      var body = document.getElementsByTagName('body')[0];
      body.appendChild(el);
    }
  }
  this.drag = {
    'after' : function (e) {
      if (!reset) {
        try {el.focus();} catch(e) {el.disabled=false; el.focus(); el.disabled=true}
        reset = true;
      }
    }
  }
  this.dragend = {
    'after' : function (e) {
      reset = false;
    }
  }
  this.shutdown = function () {
    if (el != null) {
      el.removeNode();
      el = null;
    }
  }
}
__DDI__.plugin.lockCursorAsDefault = new function() {
  this.name = 'Lock cursor';
  this.description = 'Plugin is used to show default mouse pointer during drag operation';
  this.group = 'DDI_VISUAL';
  var curel = null;
  this.drag = {
    'before' : function(e) {
      if (e.__target != curel) {
        if (curel && curel.style) curel.style.cursor = curel.__old_cursor;
        curel = e.__target;
        if (curel.style) {
          curel.__old_cursor = curel.style.cursor;
          curel.style.cursor = 'default';
        }
      }
      return true;
    }
  }
  this.dragend = {
    'before' : function(e) {
      if (curel && curel.style) curel.style.cursor = curel.__old_cursor;
      return true;
    }
  }
}
__DDI__.plugin.resizeIT = new function () {
  var self = this;
  this.name = 'Element resizer';
  this.description = 'Plugin is used to resize elements';
  this.group = 'DDI_SYSTEM';
  var el = null;
  var onresize = false;
  var vr = false;
  var hr = false;
  var dx, dy;
  var resize;
  this.runtime = {
    'always' : function (e) {
      if (__DDI__.dataTransfer) return false;
      var oldel = el;
      el = e.__target;
      if (!el || !el.__onResizeStart || onresize) {
        vr = false;
        hr = false;
        document.body.style.cursor = '';
        return;
      }
      /*
      *  check how object wants to be resized
      *  if object don't know (has no __onResizeBefore) assume both vertical and horizontal resizing
      */
      if (oldel != el) resize = el&&el.__onResizeBefore?el.__onResizeBefore(e):{'x':true,'y':true};
      var dh = el.scrollTop;
      var x = el.__layerX;
      var y = el.__layerY;
      var h = el.offsetHeight;
      var w = el.offsetWidth;
//      var cursor = (y<4?"n":(y>(h-4)?"s":""));
//          cursor = cursor + (x<4?"w":(x>(w-4)?"e":""));
//      window.status = el.innerHTML+" "+x+" "+y+" "+h+" "+w+" "+e.x+" "+e.layerX
      var cursor = "";
      cursor = (resize.y&&y>(h-12)?"s":"");
      vr = (cursor.length > 0)?false:false;
      cursor = cursor + (resize.x&&x>(w-12)?"e":"");
      hr = (cursor.length > 1 || (cursor.length > 0 && !vr))?true:false;
      if (cursor) {
        el.style.cursor = cursor+"-resize";
        //document.body.style.cursor = "move";
      }
      else {
        el.style.cursor = '';
      }
    }
  }
  this.dragstart = {
    'after' : function (e) {
       if (!vr && !hr) return false;
       if (!e.__currentTarget.__onResizeStart || e.__currentTarget.__onResizeStart(e) == false) return false;
       self.disablePluginGroup('DDI_VISUAL');
       onresize = true;
       dx = 0;
       dy = 0;
       initX = e.__currentTarget.offsetWidth;
       initY = e.__currentTarget.offsetHeight;
    }
  }
  this.drag = {
    'before' : function (e) {
      if (!onresize) return true;
      if (e.__currentTarget && e.__currentTarget.__onResize) var r = e.__currentTarget.__onResize(e);
      if (r.x.resize && hr) {
        dx += e.__dX;
        if (r.x.min<0) r.x.min = 0;
        if (r.x.max<0) r.x.max = 0;
        var ha = initX+dx;
        if (ha<=r.x.min) ha = r.x.min;
        else if (r.x.max!=0 && ha>r.x.max) ha = r.x.max;
        e.__currentTarget.style.width = ha+'px';
      }
      if (r.y.resize && vr) {
        dy += e.__dY;
        if (r.y.min<0) r.y.min = 0;
        if (r.y.max<0) r.y.max = 0;
        var va = initY+dy;
        if (va<=r.y.min) va = r.y.min;
        else if (r.y.max!=0 && va>r.y.max) va = r.y.max;
        e.__currentTarget.style.height = va+'px';
      }

      return false;
    },
    'after' : function (e) {
      if (!onresize) return;
      if (e.__currentTarget.__onResize) var r = e.__currentTarget.__onResize(e);
    }
  }
  this.dragend = {
    'after' : function (e) {
      onresize = false;
      vr = false;
      hr = false;
      self.enablePluginGroup('DDI_VISUAL');
    }
  }
};
__DDI__.plugin.moveIT = new function () {
  var self = this;
  this.name = 'Element mover';
  this.description = 'Plugin is used to move elements';
  this.group = 'DDI_SYSTEM';
  /*
  *  shows move state
  */
  var onmove = false;
  /*
  *  stores mouse coordinates relative to __currentTarget
  */
  var dx, dy;
  /*
  *  currently moved object
  */
  var element = null;
  this.dragstart = {
    'after' : function (e) {
       if (onmove || !e.__currentTarget.__onMoveStart || e.__currentTarget.__onMoveStart(e) == false) return;
       onmove = true;
       dx = e.__pageX-e.__currentTargetOffsetX;
       dy = e.__pageY-e.__currentTargetOffsetY;
       element = e.__currentTarget;
       self.disablePluginGroup('DDI_VISUAL');
    }
  }
  this.drag = {
    'before' : function (e) {
       if (!onmove || !element) return true;
       if (element.__onMove) var r = element.__onMove(e)

       var top = e.__pageY-dy;
       if (r.y.min != null && top<r.y.min) top = r.y.min;
       var bottom = top + element.offsetHeight;
       if (r.y.max != null && bottom>r.y.max) top = bottom - element.offsetHeight;
       var left = e.__pageX-dx;
       if (r.x.min != null && left<r.x.min) left = r.x.min;
       var right = left + element.offsetWidth;
       if (r.x.max != null && right>r.x.max) left = right - element.offsetWidth;
       if (r.y.move && (r.y.min==null || top>=r.y.min) && (r.y.max==null || bottom<=r.y.max)) element.style.top = top + 'px';
       if (r.x.move && (r.x.min==null || left>=r.x.min) && (r.x.max==null || right<=r.x.max)) element.style.left = left + 'px';
       return false;
    }
  }
  this.dragend = {
    'after' : function (e) {
       onmove = false;
       element = false;
       self.enablePluginGroup('DDI_VISUAL');
    }
  }
} ;

// start initialization
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
// -- end initialization