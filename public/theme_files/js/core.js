/*! Core v5.0.6 */
/*!
 * jQuery JavaScript Library v3.6.4
 * https://jquery.com/
 *
 * Includes Sizzle.js
 * https://sizzlejs.com/
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license
 * https://jquery.org/license
 *
 * Date: 2023-03-08T15:28Z
 */
( function( global, factory ) {

	"use strict";

	if ( typeof module === "object" && typeof module.exports === "object" ) {

		// For CommonJS and CommonJS-like environments where a proper `window`
		// is present, execute the factory and get jQuery.
		// For environments that do not have a `window` with a `document`
		// (such as Node.js), expose a factory as module.exports.
		// This accentuates the need for the creation of a real `window`.
		// e.g. var jQuery = require("jquery")(window);
		// See ticket trac-14549 for more info.
		module.exports = global.document ?
			factory( global, true ) :
			function( w ) {
				if ( !w.document ) {
					throw new Error( "jQuery requires a window with a document" );
				}
				return factory( w );
			};
	} else {
		factory( global );
	}

// Pass this if window is not defined yet
} )( typeof window !== "undefined" ? window : this, function( window, noGlobal ) {

// Edge <= 12 - 13+, Firefox <=18 - 45+, IE 10 - 11, Safari 5.1 - 9+, iOS 6 - 9.1
// throw exceptions when non-strict code (e.g., ASP.NET 4.5) accesses strict mode
// arguments.callee.caller (trac-13335). But as of jQuery 3.0 (2016), strict mode should be common
// enough that all such attempts are guarded in a try block.
"use strict";

var arr = [];

var getProto = Object.getPrototypeOf;

var slice = arr.slice;

var flat = arr.flat ? function( array ) {
	return arr.flat.call( array );
} : function( array ) {
	return arr.concat.apply( [], array );
};


var push = arr.push;

var indexOf = arr.indexOf;

var class2type = {};

var toString = class2type.toString;

var hasOwn = class2type.hasOwnProperty;

var fnToString = hasOwn.toString;

var ObjectFunctionString = fnToString.call( Object );

var support = {};

var isFunction = function isFunction( obj ) {

		// Support: Chrome <=57, Firefox <=52
		// In some browsers, typeof returns "function" for HTML <object> elements
		// (i.e., `typeof document.createElement( "object" ) === "function"`).
		// We don't want to classify *any* DOM node as a function.
		// Support: QtWeb <=3.8.5, WebKit <=534.34, wkhtmltopdf tool <=0.12.5
		// Plus for old WebKit, typeof returns "function" for HTML collections
		// (e.g., `typeof document.getElementsByTagName("div") === "function"`). (gh-4756)
		return typeof obj === "function" && typeof obj.nodeType !== "number" &&
			typeof obj.item !== "function";
	};


var isWindow = function isWindow( obj ) {
		return obj != null && obj === obj.window;
	};


var document = window.document;



	var preservedScriptAttributes = {
		type: true,
		src: true,
		nonce: true,
		noModule: true
	};

	function DOMEval( code, node, doc ) {
		doc = doc || document;

		var i, val,
			script = doc.createElement( "script" );

		script.text = code;
		if ( node ) {
			for ( i in preservedScriptAttributes ) {

				// Support: Firefox 64+, Edge 18+
				// Some browsers don't support the "nonce" property on scripts.
				// On the other hand, just using `getAttribute` is not enough as
				// the `nonce` attribute is reset to an empty string whenever it
				// becomes browsing-context connected.
				// See https://github.com/whatwg/html/issues/2369
				// See https://html.spec.whatwg.org/#nonce-attributes
				// The `node.getAttribute` check was added for the sake of
				// `jQuery.globalEval` so that it can fake a nonce-containing node
				// via an object.
				val = node[ i ] || node.getAttribute && node.getAttribute( i );
				if ( val ) {
					script.setAttribute( i, val );
				}
			}
		}
		doc.head.appendChild( script ).parentNode.removeChild( script );
	}


function toType( obj ) {
	if ( obj == null ) {
		return obj + "";
	}

	// Support: Android <=2.3 only (functionish RegExp)
	return typeof obj === "object" || typeof obj === "function" ?
		class2type[ toString.call( obj ) ] || "object" :
		typeof obj;
}
/* global Symbol */
// Defining this global in .eslintrc.json would create a danger of using the global
// unguarded in another place, it seems safer to define global only for this module



var
	version = "3.6.4",

	// Define a local copy of jQuery
	jQuery = function( selector, context ) {

		// The jQuery object is actually just the init constructor 'enhanced'
		// Need init if jQuery is called (just allow error to be thrown if not included)
		return new jQuery.fn.init( selector, context );
	};

jQuery.fn = jQuery.prototype = {

	// The current version of jQuery being used
	jquery: version,

	constructor: jQuery,

	// The default length of a jQuery object is 0
	length: 0,

	toArray: function() {
		return slice.call( this );
	},

	// Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get: function( num ) {

		// Return all the elements in a clean array
		if ( num == null ) {
			return slice.call( this );
		}

		// Return just the one element from the set
		return num < 0 ? this[ num + this.length ] : this[ num ];
	},

	// Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack: function( elems ) {

		// Build a new jQuery matched element set
		var ret = jQuery.merge( this.constructor(), elems );

		// Add the old object onto the stack (as a reference)
		ret.prevObject = this;

		// Return the newly-formed element set
		return ret;
	},

	// Execute a callback for every element in the matched set.
	each: function( callback ) {
		return jQuery.each( this, callback );
	},

	map: function( callback ) {
		return this.pushStack( jQuery.map( this, function( elem, i ) {
			return callback.call( elem, i, elem );
		} ) );
	},

	slice: function() {
		return this.pushStack( slice.apply( this, arguments ) );
	},

	first: function() {
		return this.eq( 0 );
	},

	last: function() {
		return this.eq( -1 );
	},

	even: function() {
		return this.pushStack( jQuery.grep( this, function( _elem, i ) {
			return ( i + 1 ) % 2;
		} ) );
	},

	odd: function() {
		return this.pushStack( jQuery.grep( this, function( _elem, i ) {
			return i % 2;
		} ) );
	},

	eq: function( i ) {
		var len = this.length,
			j = +i + ( i < 0 ? len : 0 );
		return this.pushStack( j >= 0 && j < len ? [ this[ j ] ] : [] );
	},

	end: function() {
		return this.prevObject || this.constructor();
	},

	// For internal use only.
	// Behaves like an Array's method, not like a jQuery method.
	push: push,
	sort: arr.sort,
	splice: arr.splice
};

jQuery.extend = jQuery.fn.extend = function() {
	var options, name, src, copy, copyIsArray, clone,
		target = arguments[ 0 ] || {},
		i = 1,
		length = arguments.length,
		deep = false;

	// Handle a deep copy situation
	if ( typeof target === "boolean" ) {
		deep = target;

		// Skip the boolean and the target
		target = arguments[ i ] || {};
		i++;
	}

	// Handle case when target is a string or something (possible in deep copy)
	if ( typeof target !== "object" && !isFunction( target ) ) {
		target = {};
	}

	// Extend jQuery itself if only one argument is passed
	if ( i === length ) {
		target = this;
		i--;
	}

	for ( ; i < length; i++ ) {

		// Only deal with non-null/undefined values
		if ( ( options = arguments[ i ] ) != null ) {

			// Extend the base object
			for ( name in options ) {
				copy = options[ name ];

				// Prevent Object.prototype pollution
				// Prevent never-ending loop
				if ( name === "__proto__" || target === copy ) {
					continue;
				}

				// Recurse if we're merging plain objects or arrays
				if ( deep && copy && ( jQuery.isPlainObject( copy ) ||
					( copyIsArray = Array.isArray( copy ) ) ) ) {
					src = target[ name ];

					// Ensure proper type for the source value
					if ( copyIsArray && !Array.isArray( src ) ) {
						clone = [];
					} else if ( !copyIsArray && !jQuery.isPlainObject( src ) ) {
						clone = {};
					} else {
						clone = src;
					}
					copyIsArray = false;

					// Never move original objects, clone them
					target[ name ] = jQuery.extend( deep, clone, copy );

				// Don't bring in undefined values
				} else if ( copy !== undefined ) {
					target[ name ] = copy;
				}
			}
		}
	}

	// Return the modified object
	return target;
};

jQuery.extend( {

	// Unique for each copy of jQuery on the page
	expando: "jQuery" + ( version + Math.random() ).replace( /\D/g, "" ),

	// Assume jQuery is ready without the ready module
	isReady: true,

	error: function( msg ) {
		throw new Error( msg );
	},

	noop: function() {},

	isPlainObject: function( obj ) {
		var proto, Ctor;

		// Detect obvious negatives
		// Use toString instead of jQuery.type to catch host objects
		if ( !obj || toString.call( obj ) !== "[object Object]" ) {
			return false;
		}

		proto = getProto( obj );

		// Objects with no prototype (e.g., `Object.create( null )`) are plain
		if ( !proto ) {
			return true;
		}

		// Objects with prototype are plain iff they were constructed by a global Object function
		Ctor = hasOwn.call( proto, "constructor" ) && proto.constructor;
		return typeof Ctor === "function" && fnToString.call( Ctor ) === ObjectFunctionString;
	},

	isEmptyObject: function( obj ) {
		var name;

		for ( name in obj ) {
			return false;
		}
		return true;
	},

	// Evaluates a script in a provided context; falls back to the global one
	// if not specified.
	globalEval: function( code, options, doc ) {
		DOMEval( code, { nonce: options && options.nonce }, doc );
	},

	each: function( obj, callback ) {
		var length, i = 0;

		if ( isArrayLike( obj ) ) {
			length = obj.length;
			for ( ; i < length; i++ ) {
				if ( callback.call( obj[ i ], i, obj[ i ] ) === false ) {
					break;
				}
			}
		} else {
			for ( i in obj ) {
				if ( callback.call( obj[ i ], i, obj[ i ] ) === false ) {
					break;
				}
			}
		}

		return obj;
	},

	// results is for internal usage only
	makeArray: function( arr, results ) {
		var ret = results || [];

		if ( arr != null ) {
			if ( isArrayLike( Object( arr ) ) ) {
				jQuery.merge( ret,
					typeof arr === "string" ?
						[ arr ] : arr
				);
			} else {
				push.call( ret, arr );
			}
		}

		return ret;
	},

	inArray: function( elem, arr, i ) {
		return arr == null ? -1 : indexOf.call( arr, elem, i );
	},

	// Support: Android <=4.0 only, PhantomJS 1 only
	// push.apply(_, arraylike) throws on ancient WebKit
	merge: function( first, second ) {
		var len = +second.length,
			j = 0,
			i = first.length;

		for ( ; j < len; j++ ) {
			first[ i++ ] = second[ j ];
		}

		first.length = i;

		return first;
	},

	grep: function( elems, callback, invert ) {
		var callbackInverse,
			matches = [],
			i = 0,
			length = elems.length,
			callbackExpect = !invert;

		// Go through the array, only saving the items
		// that pass the validator function
		for ( ; i < length; i++ ) {
			callbackInverse = !callback( elems[ i ], i );
			if ( callbackInverse !== callbackExpect ) {
				matches.push( elems[ i ] );
			}
		}

		return matches;
	},

	// arg is for internal usage only
	map: function( elems, callback, arg ) {
		var length, value,
			i = 0,
			ret = [];

		// Go through the array, translating each of the items to their new values
		if ( isArrayLike( elems ) ) {
			length = elems.length;
			for ( ; i < length; i++ ) {
				value = callback( elems[ i ], i, arg );

				if ( value != null ) {
					ret.push( value );
				}
			}

		// Go through every key on the object,
		} else {
			for ( i in elems ) {
				value = callback( elems[ i ], i, arg );

				if ( value != null ) {
					ret.push( value );
				}
			}
		}

		// Flatten any nested arrays
		return flat( ret );
	},

	// A global GUID counter for objects
	guid: 1,

	// jQuery.support is not used in Core but other projects attach their
	// properties to it so it needs to exist.
	support: support
} );

if ( typeof Symbol === "function" ) {
	jQuery.fn[ Symbol.iterator ] = arr[ Symbol.iterator ];
}

// Populate the class2type map
jQuery.each( "Boolean Number String Function Array Date RegExp Object Error Symbol".split( " " ),
	function( _i, name ) {
		class2type[ "[object " + name + "]" ] = name.toLowerCase();
	} );

function isArrayLike( obj ) {

	// Support: real iOS 8.2 only (not reproducible in simulator)
	// `in` check used to prevent JIT error (gh-2145)
	// hasOwn isn't used here due to false negatives
	// regarding Nodelist length in IE
	var length = !!obj && "length" in obj && obj.length,
		type = toType( obj );

	if ( isFunction( obj ) || isWindow( obj ) ) {
		return false;
	}

	return type === "array" || length === 0 ||
		typeof length === "number" && length > 0 && ( length - 1 ) in obj;
}
var Sizzle =
/*!
 * Sizzle CSS Selector Engine v2.3.10
 * https://sizzlejs.com/
 *
 * Copyright JS Foundation and other contributors
 * Released under the MIT license
 * https://js.foundation/
 *
 * Date: 2023-02-14
 */
( function( window ) {
var i,
	support,
	Expr,
	getText,
	isXML,
	tokenize,
	compile,
	select,
	outermostContext,
	sortInput,
	hasDuplicate,

	// Local document vars
	setDocument,
	document,
	docElem,
	documentIsHTML,
	rbuggyQSA,
	rbuggyMatches,
	matches,
	contains,

	// Instance-specific data
	expando = "sizzle" + 1 * new Date(),
	preferredDoc = window.document,
	dirruns = 0,
	done = 0,
	classCache = createCache(),
	tokenCache = createCache(),
	compilerCache = createCache(),
	nonnativeSelectorCache = createCache(),
	sortOrder = function( a, b ) {
		if ( a === b ) {
			hasDuplicate = true;
		}
		return 0;
	},

	// Instance methods
	hasOwn = ( {} ).hasOwnProperty,
	arr = [],
	pop = arr.pop,
	pushNative = arr.push,
	push = arr.push,
	slice = arr.slice,

	// Use a stripped-down indexOf as it's faster than native
	// https://jsperf.com/thor-indexof-vs-for/5
	indexOf = function( list, elem ) {
		var i = 0,
			len = list.length;
		for ( ; i < len; i++ ) {
			if ( list[ i ] === elem ) {
				return i;
			}
		}
		return -1;
	},

	booleans = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|" +
		"ismap|loop|multiple|open|readonly|required|scoped",

	// Regular expressions

	// http://www.w3.org/TR/css3-selectors/#whitespace
	whitespace = "[\\x20\\t\\r\\n\\f]",

	// https://www.w3.org/TR/css-syntax-3/#ident-token-diagram
	identifier = "(?:\\\\[\\da-fA-F]{1,6}" + whitespace +
		"?|\\\\[^\\r\\n\\f]|[\\w-]|[^\0-\\x7f])+",

	// Attribute selectors: http://www.w3.org/TR/selectors/#attribute-selectors
	attributes = "\\[" + whitespace + "*(" + identifier + ")(?:" + whitespace +

		// Operator (capture 2)
		"*([*^$|!~]?=)" + whitespace +

		// "Attribute values must be CSS identifiers [capture 5]
		// or strings [capture 3 or capture 4]"
		"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + identifier + "))|)" +
		whitespace + "*\\]",

	pseudos = ":(" + identifier + ")(?:\\((" +

		// To reduce the number of selectors needing tokenize in the preFilter, prefer arguments:
		// 1. quoted (capture 3; capture 4 or capture 5)
		"('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|" +

		// 2. simple (capture 6)
		"((?:\\\\.|[^\\\\()[\\]]|" + attributes + ")*)|" +

		// 3. anything else (capture 2)
		".*" +
		")\\)|)",

	// Leading and non-escaped trailing whitespace, capturing some non-whitespace characters preceding the latter
	rwhitespace = new RegExp( whitespace + "+", "g" ),
	rtrim = new RegExp( "^" + whitespace + "+|((?:^|[^\\\\])(?:\\\\.)*)" +
		whitespace + "+$", "g" ),

	rcomma = new RegExp( "^" + whitespace + "*," + whitespace + "*" ),
	rleadingCombinator = new RegExp( "^" + whitespace + "*([>+~]|" + whitespace + ")" + whitespace +
		"*" ),
	rdescend = new RegExp( whitespace + "|>" ),

	rpseudo = new RegExp( pseudos ),
	ridentifier = new RegExp( "^" + identifier + "$" ),

	matchExpr = {
		"ID": new RegExp( "^#(" + identifier + ")" ),
		"CLASS": new RegExp( "^\\.(" + identifier + ")" ),
		"TAG": new RegExp( "^(" + identifier + "|[*])" ),
		"ATTR": new RegExp( "^" + attributes ),
		"PSEUDO": new RegExp( "^" + pseudos ),
		"CHILD": new RegExp( "^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" +
			whitespace + "*(even|odd|(([+-]|)(\\d*)n|)" + whitespace + "*(?:([+-]|)" +
			whitespace + "*(\\d+)|))" + whitespace + "*\\)|)", "i" ),
		"bool": new RegExp( "^(?:" + booleans + ")$", "i" ),

		// For use in libraries implementing .is()
		// We use this for POS matching in `select`
		"needsContext": new RegExp( "^" + whitespace +
			"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + whitespace +
			"*((?:-\\d)?\\d*)" + whitespace + "*\\)|)(?=[^-]|$)", "i" )
	},

	rhtml = /HTML$/i,
	rinputs = /^(?:input|select|textarea|button)$/i,
	rheader = /^h\d$/i,

	rnative = /^[^{]+\{\s*\[native \w/,

	// Easily-parseable/retrievable ID or TAG or CLASS selectors
	rquickExpr = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,

	rsibling = /[+~]/,

	// CSS escapes
	// http://www.w3.org/TR/CSS21/syndata.html#escaped-characters
	runescape = new RegExp( "\\\\[\\da-fA-F]{1,6}" + whitespace + "?|\\\\([^\\r\\n\\f])", "g" ),
	funescape = function( escape, nonHex ) {
		var high = "0x" + escape.slice( 1 ) - 0x10000;

		return nonHex ?

			// Strip the backslash prefix from a non-hex escape sequence
			nonHex :

			// Replace a hexadecimal escape sequence with the encoded Unicode code point
			// Support: IE <=11+
			// For values outside the Basic Multilingual Plane (BMP), manually construct a
			// surrogate pair
			high < 0 ?
				String.fromCharCode( high + 0x10000 ) :
				String.fromCharCode( high >> 10 | 0xD800, high & 0x3FF | 0xDC00 );
	},

	// CSS string/identifier serialization
	// https://drafts.csswg.org/cssom/#common-serializing-idioms
	rcssescape = /([\0-\x1f\x7f]|^-?\d)|^-$|[^\0-\x1f\x7f-\uFFFF\w-]/g,
	fcssescape = function( ch, asCodePoint ) {
		if ( asCodePoint ) {

			// U+0000 NULL becomes U+FFFD REPLACEMENT CHARACTER
			if ( ch === "\0" ) {
				return "\uFFFD";
			}

			// Control characters and (dependent upon position) numbers get escaped as code points
			return ch.slice( 0, -1 ) + "\\" +
				ch.charCodeAt( ch.length - 1 ).toString( 16 ) + " ";
		}

		// Other potentially-special ASCII characters get backslash-escaped
		return "\\" + ch;
	},

	// Used for iframes
	// See setDocument()
	// Removing the function wrapper causes a "Permission Denied"
	// error in IE
	unloadHandler = function() {
		setDocument();
	},

	inDisabledFieldset = addCombinator(
		function( elem ) {
			return elem.disabled === true && elem.nodeName.toLowerCase() === "fieldset";
		},
		{ dir: "parentNode", next: "legend" }
	);

// Optimize for push.apply( _, NodeList )
try {
	push.apply(
		( arr = slice.call( preferredDoc.childNodes ) ),
		preferredDoc.childNodes
	);

	// Support: Android<4.0
	// Detect silently failing push.apply
	// eslint-disable-next-line no-unused-expressions
	arr[ preferredDoc.childNodes.length ].nodeType;
} catch ( e ) {
	push = { apply: arr.length ?

		// Leverage slice if possible
		function( target, els ) {
			pushNative.apply( target, slice.call( els ) );
		} :

		// Support: IE<9
		// Otherwise append directly
		function( target, els ) {
			var j = target.length,
				i = 0;

			// Can't trust NodeList.length
			while ( ( target[ j++ ] = els[ i++ ] ) ) {}
			target.length = j - 1;
		}
	};
}

function Sizzle( selector, context, results, seed ) {
	var m, i, elem, nid, match, groups, newSelector,
		newContext = context && context.ownerDocument,

		// nodeType defaults to 9, since context defaults to document
		nodeType = context ? context.nodeType : 9;

	results = results || [];

	// Return early from calls with invalid selector or context
	if ( typeof selector !== "string" || !selector ||
		nodeType !== 1 && nodeType !== 9 && nodeType !== 11 ) {

		return results;
	}

	// Try to shortcut find operations (as opposed to filters) in HTML documents
	if ( !seed ) {
		setDocument( context );
		context = context || document;

		if ( documentIsHTML ) {

			// If the selector is sufficiently simple, try using a "get*By*" DOM method
			// (excepting DocumentFragment context, where the methods don't exist)
			if ( nodeType !== 11 && ( match = rquickExpr.exec( selector ) ) ) {

				// ID selector
				if ( ( m = match[ 1 ] ) ) {

					// Document context
					if ( nodeType === 9 ) {
						if ( ( elem = context.getElementById( m ) ) ) {

							// Support: IE, Opera, Webkit
							// TODO: identify versions
							// getElementById can match elements by name instead of ID
							if ( elem.id === m ) {
								results.push( elem );
								return results;
							}
						} else {
							return results;
						}

					// Element context
					} else {

						// Support: IE, Opera, Webkit
						// TODO: identify versions
						// getElementById can match elements by name instead of ID
						if ( newContext && ( elem = newContext.getElementById( m ) ) &&
							contains( context, elem ) &&
							elem.id === m ) {

							results.push( elem );
							return results;
						}
					}

				// Type selector
				} else if ( match[ 2 ] ) {
					push.apply( results, context.getElementsByTagName( selector ) );
					return results;

				// Class selector
				} else if ( ( m = match[ 3 ] ) && support.getElementsByClassName &&
					context.getElementsByClassName ) {

					push.apply( results, context.getElementsByClassName( m ) );
					return results;
				}
			}

			// Take advantage of querySelectorAll
			if ( support.qsa &&
				!nonnativeSelectorCache[ selector + " " ] &&
				( !rbuggyQSA || !rbuggyQSA.test( selector ) ) &&

				// Support: IE 8 only
				// Exclude object elements
				( nodeType !== 1 || context.nodeName.toLowerCase() !== "object" ) ) {

				newSelector = selector;
				newContext = context;

				// qSA considers elements outside a scoping root when evaluating child or
				// descendant combinators, which is not what we want.
				// In such cases, we work around the behavior by prefixing every selector in the
				// list with an ID selector referencing the scope context.
				// The technique has to be used as well when a leading combinator is used
				// as such selectors are not recognized by querySelectorAll.
				// Thanks to Andrew Dupont for this technique.
				if ( nodeType === 1 &&
					( rdescend.test( selector ) || rleadingCombinator.test( selector ) ) ) {

					// Expand context for sibling selectors
					newContext = rsibling.test( selector ) && testContext( context.parentNode ) ||
						context;

					// We can use :scope instead of the ID hack if the browser
					// supports it & if we're not changing the context.
					if ( newContext !== context || !support.scope ) {

						// Capture the context ID, setting it first if necessary
						if ( ( nid = context.getAttribute( "id" ) ) ) {
							nid = nid.replace( rcssescape, fcssescape );
						} else {
							context.setAttribute( "id", ( nid = expando ) );
						}
					}

					// Prefix every selector in the list
					groups = tokenize( selector );
					i = groups.length;
					while ( i-- ) {
						groups[ i ] = ( nid ? "#" + nid : ":scope" ) + " " +
							toSelector( groups[ i ] );
					}
					newSelector = groups.join( "," );
				}

				try {
					push.apply( results,
						newContext.querySelectorAll( newSelector )
					);
					return results;
				} catch ( qsaError ) {
					nonnativeSelectorCache( selector, true );
				} finally {
					if ( nid === expando ) {
						context.removeAttribute( "id" );
					}
				}
			}
		}
	}

	// All others
	return select( selector.replace( rtrim, "$1" ), context, results, seed );
}

/**
 * Create key-value caches of limited size
 * @returns {function(string, object)} Returns the Object data after storing it on itself with
 *	property name the (space-suffixed) string and (if the cache is larger than Expr.cacheLength)
 *	deleting the oldest entry
 */
function createCache() {
	var keys = [];

	function cache( key, value ) {

		// Use (key + " ") to avoid collision with native prototype properties (see Issue #157)
		if ( keys.push( key + " " ) > Expr.cacheLength ) {

			// Only keep the most recent entries
			delete cache[ keys.shift() ];
		}
		return ( cache[ key + " " ] = value );
	}
	return cache;
}

/**
 * Mark a function for special use by Sizzle
 * @param {Function} fn The function to mark
 */
function markFunction( fn ) {
	fn[ expando ] = true;
	return fn;
}

/**
 * Support testing using an element
 * @param {Function} fn Passed the created element and returns a boolean result
 */
function assert( fn ) {
	var el = document.createElement( "fieldset" );

	try {
		return !!fn( el );
	} catch ( e ) {
		return false;
	} finally {

		// Remove from its parent by default
		if ( el.parentNode ) {
			el.parentNode.removeChild( el );
		}

		// release memory in IE
		el = null;
	}
}

/**
 * Adds the same handler for all of the specified attrs
 * @param {String} attrs Pipe-separated list of attributes
 * @param {Function} handler The method that will be applied
 */
function addHandle( attrs, handler ) {
	var arr = attrs.split( "|" ),
		i = arr.length;

	while ( i-- ) {
		Expr.attrHandle[ arr[ i ] ] = handler;
	}
}

/**
 * Checks document order of two siblings
 * @param {Element} a
 * @param {Element} b
 * @returns {Number} Returns less than 0 if a precedes b, greater than 0 if a follows b
 */
function siblingCheck( a, b ) {
	var cur = b && a,
		diff = cur && a.nodeType === 1 && b.nodeType === 1 &&
			a.sourceIndex - b.sourceIndex;

	// Use IE sourceIndex if available on both nodes
	if ( diff ) {
		return diff;
	}

	// Check if b follows a
	if ( cur ) {
		while ( ( cur = cur.nextSibling ) ) {
			if ( cur === b ) {
				return -1;
			}
		}
	}

	return a ? 1 : -1;
}

/**
 * Returns a function to use in pseudos for input types
 * @param {String} type
 */
function createInputPseudo( type ) {
	return function( elem ) {
		var name = elem.nodeName.toLowerCase();
		return name === "input" && elem.type === type;
	};
}

/**
 * Returns a function to use in pseudos for buttons
 * @param {String} type
 */
function createButtonPseudo( type ) {
	return function( elem ) {
		var name = elem.nodeName.toLowerCase();
		return ( name === "input" || name === "button" ) && elem.type === type;
	};
}

/**
 * Returns a function to use in pseudos for :enabled/:disabled
 * @param {Boolean} disabled true for :disabled; false for :enabled
 */
function createDisabledPseudo( disabled ) {

	// Known :disabled false positives: fieldset[disabled] > legend:nth-of-type(n+2) :can-disable
	return function( elem ) {

		// Only certain elements can match :enabled or :disabled
		// https://html.spec.whatwg.org/multipage/scripting.html#selector-enabled
		// https://html.spec.whatwg.org/multipage/scripting.html#selector-disabled
		if ( "form" in elem ) {

			// Check for inherited disabledness on relevant non-disabled elements:
			// * listed form-associated elements in a disabled fieldset
			//   https://html.spec.whatwg.org/multipage/forms.html#category-listed
			//   https://html.spec.whatwg.org/multipage/forms.html#concept-fe-disabled
			// * option elements in a disabled optgroup
			//   https://html.spec.whatwg.org/multipage/forms.html#concept-option-disabled
			// All such elements have a "form" property.
			if ( elem.parentNode && elem.disabled === false ) {

				// Option elements defer to a parent optgroup if present
				if ( "label" in elem ) {
					if ( "label" in elem.parentNode ) {
						return elem.parentNode.disabled === disabled;
					} else {
						return elem.disabled === disabled;
					}
				}

				// Support: IE 6 - 11
				// Use the isDisabled shortcut property to check for disabled fieldset ancestors
				return elem.isDisabled === disabled ||

					// Where there is no isDisabled, check manually
					/* jshint -W018 */
					elem.isDisabled !== !disabled &&
					inDisabledFieldset( elem ) === disabled;
			}

			return elem.disabled === disabled;

		// Try to winnow out elements that can't be disabled before trusting the disabled property.
		// Some victims get caught in our net (label, legend, menu, track), but it shouldn't
		// even exist on them, let alone have a boolean value.
		} else if ( "label" in elem ) {
			return elem.disabled === disabled;
		}

		// Remaining elements are neither :enabled nor :disabled
		return false;
	};
}

/**
 * Returns a function to use in pseudos for positionals
 * @param {Function} fn
 */
function createPositionalPseudo( fn ) {
	return markFunction( function( argument ) {
		argument = +argument;
		return markFunction( function( seed, matches ) {
			var j,
				matchIndexes = fn( [], seed.length, argument ),
				i = matchIndexes.length;

			// Match elements found at the specified indexes
			while ( i-- ) {
				if ( seed[ ( j = matchIndexes[ i ] ) ] ) {
					seed[ j ] = !( matches[ j ] = seed[ j ] );
				}
			}
		} );
	} );
}

/**
 * Checks a node for validity as a Sizzle context
 * @param {Element|Object=} context
 * @returns {Element|Object|Boolean} The input node if acceptable, otherwise a falsy value
 */
function testContext( context ) {
	return context && typeof context.getElementsByTagName !== "undefined" && context;
}

// Expose support vars for convenience
support = Sizzle.support = {};

/**
 * Detects XML nodes
 * @param {Element|Object} elem An element or a document
 * @returns {Boolean} True iff elem is a non-HTML XML node
 */
isXML = Sizzle.isXML = function( elem ) {
	var namespace = elem && elem.namespaceURI,
		docElem = elem && ( elem.ownerDocument || elem ).documentElement;

	// Support: IE <=8
	// Assume HTML when documentElement doesn't yet exist, such as inside loading iframes
	// https://bugs.jquery.com/ticket/4833
	return !rhtml.test( namespace || docElem && docElem.nodeName || "HTML" );
};

/**
 * Sets document-related variables once based on the current document
 * @param {Element|Object} [doc] An element or document object to use to set the document
 * @returns {Object} Returns the current document
 */
setDocument = Sizzle.setDocument = function( node ) {
	var hasCompare, subWindow,
		doc = node ? node.ownerDocument || node : preferredDoc;

	// Return early if doc is invalid or already selected
	// Support: IE 11+, Edge 17 - 18+
	// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
	// two documents; shallow comparisons work.
	// eslint-disable-next-line eqeqeq
	if ( doc == document || doc.nodeType !== 9 || !doc.documentElement ) {
		return document;
	}

	// Update global variables
	document = doc;
	docElem = document.documentElement;
	documentIsHTML = !isXML( document );

	// Support: IE 9 - 11+, Edge 12 - 18+
	// Accessing iframe documents after unload throws "permission denied" errors (jQuery #13936)
	// Support: IE 11+, Edge 17 - 18+
	// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
	// two documents; shallow comparisons work.
	// eslint-disable-next-line eqeqeq
	if ( preferredDoc != document &&
		( subWindow = document.defaultView ) && subWindow.top !== subWindow ) {

		// Support: IE 11, Edge
		if ( subWindow.addEventListener ) {
			subWindow.addEventListener( "unload", unloadHandler, false );

		// Support: IE 9 - 10 only
		} else if ( subWindow.attachEvent ) {
			subWindow.attachEvent( "onunload", unloadHandler );
		}
	}

	// Support: IE 8 - 11+, Edge 12 - 18+, Chrome <=16 - 25 only, Firefox <=3.6 - 31 only,
	// Safari 4 - 5 only, Opera <=11.6 - 12.x only
	// IE/Edge & older browsers don't support the :scope pseudo-class.
	// Support: Safari 6.0 only
	// Safari 6.0 supports :scope but it's an alias of :root there.
	support.scope = assert( function( el ) {
		docElem.appendChild( el ).appendChild( document.createElement( "div" ) );
		return typeof el.querySelectorAll !== "undefined" &&
			!el.querySelectorAll( ":scope fieldset div" ).length;
	} );

	// Support: Chrome 105 - 110+, Safari 15.4 - 16.3+
	// Make sure the the `:has()` argument is parsed unforgivingly.
	// We include `*` in the test to detect buggy implementations that are
	// _selectively_ forgiving (specifically when the list includes at least
	// one valid selector).
	// Note that we treat complete lack of support for `:has()` as if it were
	// spec-compliant support, which is fine because use of `:has()` in such
	// environments will fail in the qSA path and fall back to jQuery traversal
	// anyway.
	support.cssHas = assert( function() {
		try {
			document.querySelector( ":has(*,:jqfake)" );
			return false;
		} catch ( e ) {
			return true;
		}
	} );

	/* Attributes
	---------------------------------------------------------------------- */

	// Support: IE<8
	// Verify that getAttribute really returns attributes and not properties
	// (excepting IE8 booleans)
	support.attributes = assert( function( el ) {
		el.className = "i";
		return !el.getAttribute( "className" );
	} );

	/* getElement(s)By*
	---------------------------------------------------------------------- */

	// Check if getElementsByTagName("*") returns only elements
	support.getElementsByTagName = assert( function( el ) {
		el.appendChild( document.createComment( "" ) );
		return !el.getElementsByTagName( "*" ).length;
	} );

	// Support: IE<9
	support.getElementsByClassName = rnative.test( document.getElementsByClassName );

	// Support: IE<10
	// Check if getElementById returns elements by name
	// The broken getElementById methods don't pick up programmatically-set names,
	// so use a roundabout getElementsByName test
	support.getById = assert( function( el ) {
		docElem.appendChild( el ).id = expando;
		return !document.getElementsByName || !document.getElementsByName( expando ).length;
	} );

	// ID filter and find
	if ( support.getById ) {
		Expr.filter[ "ID" ] = function( id ) {
			var attrId = id.replace( runescape, funescape );
			return function( elem ) {
				return elem.getAttribute( "id" ) === attrId;
			};
		};
		Expr.find[ "ID" ] = function( id, context ) {
			if ( typeof context.getElementById !== "undefined" && documentIsHTML ) {
				var elem = context.getElementById( id );
				return elem ? [ elem ] : [];
			}
		};
	} else {
		Expr.filter[ "ID" ] =  function( id ) {
			var attrId = id.replace( runescape, funescape );
			return function( elem ) {
				var node = typeof elem.getAttributeNode !== "undefined" &&
					elem.getAttributeNode( "id" );
				return node && node.value === attrId;
			};
		};

		// Support: IE 6 - 7 only
		// getElementById is not reliable as a find shortcut
		Expr.find[ "ID" ] = function( id, context ) {
			if ( typeof context.getElementById !== "undefined" && documentIsHTML ) {
				var node, i, elems,
					elem = context.getElementById( id );

				if ( elem ) {

					// Verify the id attribute
					node = elem.getAttributeNode( "id" );
					if ( node && node.value === id ) {
						return [ elem ];
					}

					// Fall back on getElementsByName
					elems = context.getElementsByName( id );
					i = 0;
					while ( ( elem = elems[ i++ ] ) ) {
						node = elem.getAttributeNode( "id" );
						if ( node && node.value === id ) {
							return [ elem ];
						}
					}
				}

				return [];
			}
		};
	}

	// Tag
	Expr.find[ "TAG" ] = support.getElementsByTagName ?
		function( tag, context ) {
			if ( typeof context.getElementsByTagName !== "undefined" ) {
				return context.getElementsByTagName( tag );

			// DocumentFragment nodes don't have gEBTN
			} else if ( support.qsa ) {
				return context.querySelectorAll( tag );
			}
		} :

		function( tag, context ) {
			var elem,
				tmp = [],
				i = 0,

				// By happy coincidence, a (broken) gEBTN appears on DocumentFragment nodes too
				results = context.getElementsByTagName( tag );

			// Filter out possible comments
			if ( tag === "*" ) {
				while ( ( elem = results[ i++ ] ) ) {
					if ( elem.nodeType === 1 ) {
						tmp.push( elem );
					}
				}

				return tmp;
			}
			return results;
		};

	// Class
	Expr.find[ "CLASS" ] = support.getElementsByClassName && function( className, context ) {
		if ( typeof context.getElementsByClassName !== "undefined" && documentIsHTML ) {
			return context.getElementsByClassName( className );
		}
	};

	/* QSA/matchesSelector
	---------------------------------------------------------------------- */

	// QSA and matchesSelector support

	// matchesSelector(:active) reports false when true (IE9/Opera 11.5)
	rbuggyMatches = [];

	// qSa(:focus) reports false when true (Chrome 21)
	// We allow this because of a bug in IE8/9 that throws an error
	// whenever `document.activeElement` is accessed on an iframe
	// So, we allow :focus to pass through QSA all the time to avoid the IE error
	// See https://bugs.jquery.com/ticket/13378
	rbuggyQSA = [];

	if ( ( support.qsa = rnative.test( document.querySelectorAll ) ) ) {

		// Build QSA regex
		// Regex strategy adopted from Diego Perini
		assert( function( el ) {

			var input;

			// Select is set to empty string on purpose
			// This is to test IE's treatment of not explicitly
			// setting a boolean content attribute,
			// since its presence should be enough
			// https://bugs.jquery.com/ticket/12359
			docElem.appendChild( el ).innerHTML = "<a id='" + expando + "'></a>" +
				"<select id='" + expando + "-\r\\' msallowcapture=''>" +
				"<option selected=''></option></select>";

			// Support: IE8, Opera 11-12.16
			// Nothing should be selected when empty strings follow ^= or $= or *=
			// The test attribute must be unknown in Opera but "safe" for WinRT
			// https://msdn.microsoft.com/en-us/library/ie/hh465388.aspx#attribute_section
			if ( el.querySelectorAll( "[msallowcapture^='']" ).length ) {
				rbuggyQSA.push( "[*^$]=" + whitespace + "*(?:''|\"\")" );
			}

			// Support: IE8
			// Boolean attributes and "value" are not treated correctly
			if ( !el.querySelectorAll( "[selected]" ).length ) {
				rbuggyQSA.push( "\\[" + whitespace + "*(?:value|" + booleans + ")" );
			}

			// Support: Chrome<29, Android<4.4, Safari<7.0+, iOS<7.0+, PhantomJS<1.9.8+
			if ( !el.querySelectorAll( "[id~=" + expando + "-]" ).length ) {
				rbuggyQSA.push( "~=" );
			}

			// Support: IE 11+, Edge 15 - 18+
			// IE 11/Edge don't find elements on a `[name='']` query in some cases.
			// Adding a temporary attribute to the document before the selection works
			// around the issue.
			// Interestingly, IE 10 & older don't seem to have the issue.
			input = document.createElement( "input" );
			input.setAttribute( "name", "" );
			el.appendChild( input );
			if ( !el.querySelectorAll( "[name='']" ).length ) {
				rbuggyQSA.push( "\\[" + whitespace + "*name" + whitespace + "*=" +
					whitespace + "*(?:''|\"\")" );
			}

			// Webkit/Opera - :checked should return selected option elements
			// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
			// IE8 throws error here and will not see later tests
			if ( !el.querySelectorAll( ":checked" ).length ) {
				rbuggyQSA.push( ":checked" );
			}

			// Support: Safari 8+, iOS 8+
			// https://bugs.webkit.org/show_bug.cgi?id=136851
			// In-page `selector#id sibling-combinator selector` fails
			if ( !el.querySelectorAll( "a#" + expando + "+*" ).length ) {
				rbuggyQSA.push( ".#.+[+~]" );
			}

			// Support: Firefox <=3.6 - 5 only
			// Old Firefox doesn't throw on a badly-escaped identifier.
			el.querySelectorAll( "\\\f" );
			rbuggyQSA.push( "[\\r\\n\\f]" );
		} );

		assert( function( el ) {
			el.innerHTML = "<a href='' disabled='disabled'></a>" +
				"<select disabled='disabled'><option/></select>";

			// Support: Windows 8 Native Apps
			// The type and name attributes are restricted during .innerHTML assignment
			var input = document.createElement( "input" );
			input.setAttribute( "type", "hidden" );
			el.appendChild( input ).setAttribute( "name", "D" );

			// Support: IE8
			// Enforce case-sensitivity of name attribute
			if ( el.querySelectorAll( "[name=d]" ).length ) {
				rbuggyQSA.push( "name" + whitespace + "*[*^$|!~]?=" );
			}

			// FF 3.5 - :enabled/:disabled and hidden elements (hidden elements are still enabled)
			// IE8 throws error here and will not see later tests
			if ( el.querySelectorAll( ":enabled" ).length !== 2 ) {
				rbuggyQSA.push( ":enabled", ":disabled" );
			}

			// Support: IE9-11+
			// IE's :disabled selector does not pick up the children of disabled fieldsets
			docElem.appendChild( el ).disabled = true;
			if ( el.querySelectorAll( ":disabled" ).length !== 2 ) {
				rbuggyQSA.push( ":enabled", ":disabled" );
			}

			// Support: Opera 10 - 11 only
			// Opera 10-11 does not throw on post-comma invalid pseudos
			el.querySelectorAll( "*,:x" );
			rbuggyQSA.push( ",.*:" );
		} );
	}

	if ( ( support.matchesSelector = rnative.test( ( matches = docElem.matches ||
		docElem.webkitMatchesSelector ||
		docElem.mozMatchesSelector ||
		docElem.oMatchesSelector ||
		docElem.msMatchesSelector ) ) ) ) {

		assert( function( el ) {

			// Check to see if it's possible to do matchesSelector
			// on a disconnected node (IE 9)
			support.disconnectedMatch = matches.call( el, "*" );

			// This should fail with an exception
			// Gecko does not error, returns false instead
			matches.call( el, "[s!='']:x" );
			rbuggyMatches.push( "!=", pseudos );
		} );
	}

	if ( !support.cssHas ) {

		// Support: Chrome 105 - 110+, Safari 15.4 - 16.3+
		// Our regular `try-catch` mechanism fails to detect natively-unsupported
		// pseudo-classes inside `:has()` (such as `:has(:contains("Foo"))`)
		// in browsers that parse the `:has()` argument as a forgiving selector list.
		// https://drafts.csswg.org/selectors/#relational now requires the argument
		// to be parsed unforgivingly, but browsers have not yet fully adjusted.
		rbuggyQSA.push( ":has" );
	}

	rbuggyQSA = rbuggyQSA.length && new RegExp( rbuggyQSA.join( "|" ) );
	rbuggyMatches = rbuggyMatches.length && new RegExp( rbuggyMatches.join( "|" ) );

	/* Contains
	---------------------------------------------------------------------- */
	hasCompare = rnative.test( docElem.compareDocumentPosition );

	// Element contains another
	// Purposefully self-exclusive
	// As in, an element does not contain itself
	contains = hasCompare || rnative.test( docElem.contains ) ?
		function( a, b ) {

			// Support: IE <9 only
			// IE doesn't have `contains` on `document` so we need to check for
			// `documentElement` presence.
			// We need to fall back to `a` when `documentElement` is missing
			// as `ownerDocument` of elements within `<template/>` may have
			// a null one - a default behavior of all modern browsers.
			var adown = a.nodeType === 9 && a.documentElement || a,
				bup = b && b.parentNode;
			return a === bup || !!( bup && bup.nodeType === 1 && (
				adown.contains ?
					adown.contains( bup ) :
					a.compareDocumentPosition && a.compareDocumentPosition( bup ) & 16
			) );
		} :
		function( a, b ) {
			if ( b ) {
				while ( ( b = b.parentNode ) ) {
					if ( b === a ) {
						return true;
					}
				}
			}
			return false;
		};

	/* Sorting
	---------------------------------------------------------------------- */

	// Document order sorting
	sortOrder = hasCompare ?
	function( a, b ) {

		// Flag for duplicate removal
		if ( a === b ) {
			hasDuplicate = true;
			return 0;
		}

		// Sort on method existence if only one input has compareDocumentPosition
		var compare = !a.compareDocumentPosition - !b.compareDocumentPosition;
		if ( compare ) {
			return compare;
		}

		// Calculate position if both inputs belong to the same document
		// Support: IE 11+, Edge 17 - 18+
		// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
		// two documents; shallow comparisons work.
		// eslint-disable-next-line eqeqeq
		compare = ( a.ownerDocument || a ) == ( b.ownerDocument || b ) ?
			a.compareDocumentPosition( b ) :

			// Otherwise we know they are disconnected
			1;

		// Disconnected nodes
		if ( compare & 1 ||
			( !support.sortDetached && b.compareDocumentPosition( a ) === compare ) ) {

			// Choose the first element that is related to our preferred document
			// Support: IE 11+, Edge 17 - 18+
			// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
			// two documents; shallow comparisons work.
			// eslint-disable-next-line eqeqeq
			if ( a == document || a.ownerDocument == preferredDoc &&
				contains( preferredDoc, a ) ) {
				return -1;
			}

			// Support: IE 11+, Edge 17 - 18+
			// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
			// two documents; shallow comparisons work.
			// eslint-disable-next-line eqeqeq
			if ( b == document || b.ownerDocument == preferredDoc &&
				contains( preferredDoc, b ) ) {
				return 1;
			}

			// Maintain original order
			return sortInput ?
				( indexOf( sortInput, a ) - indexOf( sortInput, b ) ) :
				0;
		}

		return compare & 4 ? -1 : 1;
	} :
	function( a, b ) {

		// Exit early if the nodes are identical
		if ( a === b ) {
			hasDuplicate = true;
			return 0;
		}

		var cur,
			i = 0,
			aup = a.parentNode,
			bup = b.parentNode,
			ap = [ a ],
			bp = [ b ];

		// Parentless nodes are either documents or disconnected
		if ( !aup || !bup ) {

			// Support: IE 11+, Edge 17 - 18+
			// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
			// two documents; shallow comparisons work.
			/* eslint-disable eqeqeq */
			return a == document ? -1 :
				b == document ? 1 :
				/* eslint-enable eqeqeq */
				aup ? -1 :
				bup ? 1 :
				sortInput ?
				( indexOf( sortInput, a ) - indexOf( sortInput, b ) ) :
				0;

		// If the nodes are siblings, we can do a quick check
		} else if ( aup === bup ) {
			return siblingCheck( a, b );
		}

		// Otherwise we need full lists of their ancestors for comparison
		cur = a;
		while ( ( cur = cur.parentNode ) ) {
			ap.unshift( cur );
		}
		cur = b;
		while ( ( cur = cur.parentNode ) ) {
			bp.unshift( cur );
		}

		// Walk down the tree looking for a discrepancy
		while ( ap[ i ] === bp[ i ] ) {
			i++;
		}

		return i ?

			// Do a sibling check if the nodes have a common ancestor
			siblingCheck( ap[ i ], bp[ i ] ) :

			// Otherwise nodes in our document sort first
			// Support: IE 11+, Edge 17 - 18+
			// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
			// two documents; shallow comparisons work.
			/* eslint-disable eqeqeq */
			ap[ i ] == preferredDoc ? -1 :
			bp[ i ] == preferredDoc ? 1 :
			/* eslint-enable eqeqeq */
			0;
	};

	return document;
};

Sizzle.matches = function( expr, elements ) {
	return Sizzle( expr, null, null, elements );
};

Sizzle.matchesSelector = function( elem, expr ) {
	setDocument( elem );

	if ( support.matchesSelector && documentIsHTML &&
		!nonnativeSelectorCache[ expr + " " ] &&
		( !rbuggyMatches || !rbuggyMatches.test( expr ) ) &&
		( !rbuggyQSA     || !rbuggyQSA.test( expr ) ) ) {

		try {
			var ret = matches.call( elem, expr );

			// IE 9's matchesSelector returns false on disconnected nodes
			if ( ret || support.disconnectedMatch ||

				// As well, disconnected nodes are said to be in a document
				// fragment in IE 9
				elem.document && elem.document.nodeType !== 11 ) {
				return ret;
			}
		} catch ( e ) {
			nonnativeSelectorCache( expr, true );
		}
	}

	return Sizzle( expr, document, null, [ elem ] ).length > 0;
};

Sizzle.contains = function( context, elem ) {

	// Set document vars if needed
	// Support: IE 11+, Edge 17 - 18+
	// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
	// two documents; shallow comparisons work.
	// eslint-disable-next-line eqeqeq
	if ( ( context.ownerDocument || context ) != document ) {
		setDocument( context );
	}
	return contains( context, elem );
};

Sizzle.attr = function( elem, name ) {

	// Set document vars if needed
	// Support: IE 11+, Edge 17 - 18+
	// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
	// two documents; shallow comparisons work.
	// eslint-disable-next-line eqeqeq
	if ( ( elem.ownerDocument || elem ) != document ) {
		setDocument( elem );
	}

	var fn = Expr.attrHandle[ name.toLowerCase() ],

		// Don't get fooled by Object.prototype properties (jQuery #13807)
		val = fn && hasOwn.call( Expr.attrHandle, name.toLowerCase() ) ?
			fn( elem, name, !documentIsHTML ) :
			undefined;

	return val !== undefined ?
		val :
		support.attributes || !documentIsHTML ?
			elem.getAttribute( name ) :
			( val = elem.getAttributeNode( name ) ) && val.specified ?
				val.value :
				null;
};

Sizzle.escape = function( sel ) {
	return ( sel + "" ).replace( rcssescape, fcssescape );
};

Sizzle.error = function( msg ) {
	throw new Error( "Syntax error, unrecognized expression: " + msg );
};

/**
 * Document sorting and removing duplicates
 * @param {ArrayLike} results
 */
Sizzle.uniqueSort = function( results ) {
	var elem,
		duplicates = [],
		j = 0,
		i = 0;

	// Unless we *know* we can detect duplicates, assume their presence
	hasDuplicate = !support.detectDuplicates;
	sortInput = !support.sortStable && results.slice( 0 );
	results.sort( sortOrder );

	if ( hasDuplicate ) {
		while ( ( elem = results[ i++ ] ) ) {
			if ( elem === results[ i ] ) {
				j = duplicates.push( i );
			}
		}
		while ( j-- ) {
			results.splice( duplicates[ j ], 1 );
		}
	}

	// Clear input after sorting to release objects
	// See https://github.com/jquery/sizzle/pull/225
	sortInput = null;

	return results;
};

/**
 * Utility function for retrieving the text value of an array of DOM nodes
 * @param {Array|Element} elem
 */
getText = Sizzle.getText = function( elem ) {
	var node,
		ret = "",
		i = 0,
		nodeType = elem.nodeType;

	if ( !nodeType ) {

		// If no nodeType, this is expected to be an array
		while ( ( node = elem[ i++ ] ) ) {

			// Do not traverse comment nodes
			ret += getText( node );
		}
	} else if ( nodeType === 1 || nodeType === 9 || nodeType === 11 ) {

		// Use textContent for elements
		// innerText usage removed for consistency of new lines (jQuery #11153)
		if ( typeof elem.textContent === "string" ) {
			return elem.textContent;
		} else {

			// Traverse its children
			for ( elem = elem.firstChild; elem; elem = elem.nextSibling ) {
				ret += getText( elem );
			}
		}
	} else if ( nodeType === 3 || nodeType === 4 ) {
		return elem.nodeValue;
	}

	// Do not include comment or processing instruction nodes

	return ret;
};

Expr = Sizzle.selectors = {

	// Can be adjusted by the user
	cacheLength: 50,

	createPseudo: markFunction,

	match: matchExpr,

	attrHandle: {},

	find: {},

	relative: {
		">": { dir: "parentNode", first: true },
		" ": { dir: "parentNode" },
		"+": { dir: "previousSibling", first: true },
		"~": { dir: "previousSibling" }
	},

	preFilter: {
		"ATTR": function( match ) {
			match[ 1 ] = match[ 1 ].replace( runescape, funescape );

			// Move the given value to match[3] whether quoted or unquoted
			match[ 3 ] = ( match[ 3 ] || match[ 4 ] ||
				match[ 5 ] || "" ).replace( runescape, funescape );

			if ( match[ 2 ] === "~=" ) {
				match[ 3 ] = " " + match[ 3 ] + " ";
			}

			return match.slice( 0, 4 );
		},

		"CHILD": function( match ) {

			/* matches from matchExpr["CHILD"]
				1 type (only|nth|...)
				2 what (child|of-type)
				3 argument (even|odd|\d*|\d*n([+-]\d+)?|...)
				4 xn-component of xn+y argument ([+-]?\d*n|)
				5 sign of xn-component
				6 x of xn-component
				7 sign of y-component
				8 y of y-component
			*/
			match[ 1 ] = match[ 1 ].toLowerCase();

			if ( match[ 1 ].slice( 0, 3 ) === "nth" ) {

				// nth-* requires argument
				if ( !match[ 3 ] ) {
					Sizzle.error( match[ 0 ] );
				}

				// numeric x and y parameters for Expr.filter.CHILD
				// remember that false/true cast respectively to 0/1
				match[ 4 ] = +( match[ 4 ] ?
					match[ 5 ] + ( match[ 6 ] || 1 ) :
					2 * ( match[ 3 ] === "even" || match[ 3 ] === "odd" ) );
				match[ 5 ] = +( ( match[ 7 ] + match[ 8 ] ) || match[ 3 ] === "odd" );

				// other types prohibit arguments
			} else if ( match[ 3 ] ) {
				Sizzle.error( match[ 0 ] );
			}

			return match;
		},

		"PSEUDO": function( match ) {
			var excess,
				unquoted = !match[ 6 ] && match[ 2 ];

			if ( matchExpr[ "CHILD" ].test( match[ 0 ] ) ) {
				return null;
			}

			// Accept quoted arguments as-is
			if ( match[ 3 ] ) {
				match[ 2 ] = match[ 4 ] || match[ 5 ] || "";

			// Strip excess characters from unquoted arguments
			} else if ( unquoted && rpseudo.test( unquoted ) &&

				// Get excess from tokenize (recursively)
				( excess = tokenize( unquoted, true ) ) &&

				// advance to the next closing parenthesis
				( excess = unquoted.indexOf( ")", unquoted.length - excess ) - unquoted.length ) ) {

				// excess is a negative index
				match[ 0 ] = match[ 0 ].slice( 0, excess );
				match[ 2 ] = unquoted.slice( 0, excess );
			}

			// Return only captures needed by the pseudo filter method (type and argument)
			return match.slice( 0, 3 );
		}
	},

	filter: {

		"TAG": function( nodeNameSelector ) {
			var nodeName = nodeNameSelector.replace( runescape, funescape ).toLowerCase();
			return nodeNameSelector === "*" ?
				function() {
					return true;
				} :
				function( elem ) {
					return elem.nodeName && elem.nodeName.toLowerCase() === nodeName;
				};
		},

		"CLASS": function( className ) {
			var pattern = classCache[ className + " " ];

			return pattern ||
				( pattern = new RegExp( "(^|" + whitespace +
					")" + className + "(" + whitespace + "|$)" ) ) && classCache(
						className, function( elem ) {
							return pattern.test(
								typeof elem.className === "string" && elem.className ||
								typeof elem.getAttribute !== "undefined" &&
									elem.getAttribute( "class" ) ||
								""
							);
				} );
		},

		"ATTR": function( name, operator, check ) {
			return function( elem ) {
				var result = Sizzle.attr( elem, name );

				if ( result == null ) {
					return operator === "!=";
				}
				if ( !operator ) {
					return true;
				}

				result += "";

				/* eslint-disable max-len */

				return operator === "=" ? result === check :
					operator === "!=" ? result !== check :
					operator === "^=" ? check && result.indexOf( check ) === 0 :
					operator === "*=" ? check && result.indexOf( check ) > -1 :
					operator === "$=" ? check && result.slice( -check.length ) === check :
					operator === "~=" ? ( " " + result.replace( rwhitespace, " " ) + " " ).indexOf( check ) > -1 :
					operator === "|=" ? result === check || result.slice( 0, check.length + 1 ) === check + "-" :
					false;
				/* eslint-enable max-len */

			};
		},

		"CHILD": function( type, what, _argument, first, last ) {
			var simple = type.slice( 0, 3 ) !== "nth",
				forward = type.slice( -4 ) !== "last",
				ofType = what === "of-type";

			return first === 1 && last === 0 ?

				// Shortcut for :nth-*(n)
				function( elem ) {
					return !!elem.parentNode;
				} :

				function( elem, _context, xml ) {
					var cache, uniqueCache, outerCache, node, nodeIndex, start,
						dir = simple !== forward ? "nextSibling" : "previousSibling",
						parent = elem.parentNode,
						name = ofType && elem.nodeName.toLowerCase(),
						useCache = !xml && !ofType,
						diff = false;

					if ( parent ) {

						// :(first|last|only)-(child|of-type)
						if ( simple ) {
							while ( dir ) {
								node = elem;
								while ( ( node = node[ dir ] ) ) {
									if ( ofType ?
										node.nodeName.toLowerCase() === name :
										node.nodeType === 1 ) {

										return false;
									}
								}

								// Reverse direction for :only-* (if we haven't yet done so)
								start = dir = type === "only" && !start && "nextSibling";
							}
							return true;
						}

						start = [ forward ? parent.firstChild : parent.lastChild ];

						// non-xml :nth-child(...) stores cache data on `parent`
						if ( forward && useCache ) {

							// Seek `elem` from a previously-cached index

							// ...in a gzip-friendly way
							node = parent;
							outerCache = node[ expando ] || ( node[ expando ] = {} );

							// Support: IE <9 only
							// Defend against cloned attroperties (jQuery gh-1709)
							uniqueCache = outerCache[ node.uniqueID ] ||
								( outerCache[ node.uniqueID ] = {} );

							cache = uniqueCache[ type ] || [];
							nodeIndex = cache[ 0 ] === dirruns && cache[ 1 ];
							diff = nodeIndex && cache[ 2 ];
							node = nodeIndex && parent.childNodes[ nodeIndex ];

							while ( ( node = ++nodeIndex && node && node[ dir ] ||

								// Fallback to seeking `elem` from the start
								( diff = nodeIndex = 0 ) || start.pop() ) ) {

								// When found, cache indexes on `parent` and break
								if ( node.nodeType === 1 && ++diff && node === elem ) {
									uniqueCache[ type ] = [ dirruns, nodeIndex, diff ];
									break;
								}
							}

						} else {

							// Use previously-cached element index if available
							if ( useCache ) {

								// ...in a gzip-friendly way
								node = elem;
								outerCache = node[ expando ] || ( node[ expando ] = {} );

								// Support: IE <9 only
								// Defend against cloned attroperties (jQuery gh-1709)
								uniqueCache = outerCache[ node.uniqueID ] ||
									( outerCache[ node.uniqueID ] = {} );

								cache = uniqueCache[ type ] || [];
								nodeIndex = cache[ 0 ] === dirruns && cache[ 1 ];
								diff = nodeIndex;
							}

							// xml :nth-child(...)
							// or :nth-last-child(...) or :nth(-last)?-of-type(...)
							if ( diff === false ) {

								// Use the same loop as above to seek `elem` from the start
								while ( ( node = ++nodeIndex && node && node[ dir ] ||
									( diff = nodeIndex = 0 ) || start.pop() ) ) {

									if ( ( ofType ?
										node.nodeName.toLowerCase() === name :
										node.nodeType === 1 ) &&
										++diff ) {

										// Cache the index of each encountered element
										if ( useCache ) {
											outerCache = node[ expando ] ||
												( node[ expando ] = {} );

											// Support: IE <9 only
											// Defend against cloned attroperties (jQuery gh-1709)
											uniqueCache = outerCache[ node.uniqueID ] ||
												( outerCache[ node.uniqueID ] = {} );

											uniqueCache[ type ] = [ dirruns, diff ];
										}

										if ( node === elem ) {
											break;
										}
									}
								}
							}
						}

						// Incorporate the offset, then check against cycle size
						diff -= last;
						return diff === first || ( diff % first === 0 && diff / first >= 0 );
					}
				};
		},

		"PSEUDO": function( pseudo, argument ) {

			// pseudo-class names are case-insensitive
			// http://www.w3.org/TR/selectors/#pseudo-classes
			// Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
			// Remember that setFilters inherits from pseudos
			var args,
				fn = Expr.pseudos[ pseudo ] || Expr.setFilters[ pseudo.toLowerCase() ] ||
					Sizzle.error( "unsupported pseudo: " + pseudo );

			// The user may use createPseudo to indicate that
			// arguments are needed to create the filter function
			// just as Sizzle does
			if ( fn[ expando ] ) {
				return fn( argument );
			}

			// But maintain support for old signatures
			if ( fn.length > 1 ) {
				args = [ pseudo, pseudo, "", argument ];
				return Expr.setFilters.hasOwnProperty( pseudo.toLowerCase() ) ?
					markFunction( function( seed, matches ) {
						var idx,
							matched = fn( seed, argument ),
							i = matched.length;
						while ( i-- ) {
							idx = indexOf( seed, matched[ i ] );
							seed[ idx ] = !( matches[ idx ] = matched[ i ] );
						}
					} ) :
					function( elem ) {
						return fn( elem, 0, args );
					};
			}

			return fn;
		}
	},

	pseudos: {

		// Potentially complex pseudos
		"not": markFunction( function( selector ) {

			// Trim the selector passed to compile
			// to avoid treating leading and trailing
			// spaces as combinators
			var input = [],
				results = [],
				matcher = compile( selector.replace( rtrim, "$1" ) );

			return matcher[ expando ] ?
				markFunction( function( seed, matches, _context, xml ) {
					var elem,
						unmatched = matcher( seed, null, xml, [] ),
						i = seed.length;

					// Match elements unmatched by `matcher`
					while ( i-- ) {
						if ( ( elem = unmatched[ i ] ) ) {
							seed[ i ] = !( matches[ i ] = elem );
						}
					}
				} ) :
				function( elem, _context, xml ) {
					input[ 0 ] = elem;
					matcher( input, null, xml, results );

					// Don't keep the element (issue #299)
					input[ 0 ] = null;
					return !results.pop();
				};
		} ),

		"has": markFunction( function( selector ) {
			return function( elem ) {
				return Sizzle( selector, elem ).length > 0;
			};
		} ),

		"contains": markFunction( function( text ) {
			text = text.replace( runescape, funescape );
			return function( elem ) {
				return ( elem.textContent || getText( elem ) ).indexOf( text ) > -1;
			};
		} ),

		// "Whether an element is represented by a :lang() selector
		// is based solely on the element's language value
		// being equal to the identifier C,
		// or beginning with the identifier C immediately followed by "-".
		// The matching of C against the element's language value is performed case-insensitively.
		// The identifier C does not have to be a valid language name."
		// http://www.w3.org/TR/selectors/#lang-pseudo
		"lang": markFunction( function( lang ) {

			// lang value must be a valid identifier
			if ( !ridentifier.test( lang || "" ) ) {
				Sizzle.error( "unsupported lang: " + lang );
			}
			lang = lang.replace( runescape, funescape ).toLowerCase();
			return function( elem ) {
				var elemLang;
				do {
					if ( ( elemLang = documentIsHTML ?
						elem.lang :
						elem.getAttribute( "xml:lang" ) || elem.getAttribute( "lang" ) ) ) {

						elemLang = elemLang.toLowerCase();
						return elemLang === lang || elemLang.indexOf( lang + "-" ) === 0;
					}
				} while ( ( elem = elem.parentNode ) && elem.nodeType === 1 );
				return false;
			};
		} ),

		// Miscellaneous
		"target": function( elem ) {
			var hash = window.location && window.location.hash;
			return hash && hash.slice( 1 ) === elem.id;
		},

		"root": function( elem ) {
			return elem === docElem;
		},

		"focus": function( elem ) {
			return elem === document.activeElement &&
				( !document.hasFocus || document.hasFocus() ) &&
				!!( elem.type || elem.href || ~elem.tabIndex );
		},

		// Boolean properties
		"enabled": createDisabledPseudo( false ),
		"disabled": createDisabledPseudo( true ),

		"checked": function( elem ) {

			// In CSS3, :checked should return both checked and selected elements
			// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
			var nodeName = elem.nodeName.toLowerCase();
			return ( nodeName === "input" && !!elem.checked ) ||
				( nodeName === "option" && !!elem.selected );
		},

		"selected": function( elem ) {

			// Accessing this property makes selected-by-default
			// options in Safari work properly
			if ( elem.parentNode ) {
				// eslint-disable-next-line no-unused-expressions
				elem.parentNode.selectedIndex;
			}

			return elem.selected === true;
		},

		// Contents
		"empty": function( elem ) {

			// http://www.w3.org/TR/selectors/#empty-pseudo
			// :empty is negated by element (1) or content nodes (text: 3; cdata: 4; entity ref: 5),
			//   but not by others (comment: 8; processing instruction: 7; etc.)
			// nodeType < 6 works because attributes (2) do not appear as children
			for ( elem = elem.firstChild; elem; elem = elem.nextSibling ) {
				if ( elem.nodeType < 6 ) {
					return false;
				}
			}
			return true;
		},

		"parent": function( elem ) {
			return !Expr.pseudos[ "empty" ]( elem );
		},

		// Element/input types
		"header": function( elem ) {
			return rheader.test( elem.nodeName );
		},

		"input": function( elem ) {
			return rinputs.test( elem.nodeName );
		},

		"button": function( elem ) {
			var name = elem.nodeName.toLowerCase();
			return name === "input" && elem.type === "button" || name === "button";
		},

		"text": function( elem ) {
			var attr;
			return elem.nodeName.toLowerCase() === "input" &&
				elem.type === "text" &&

				// Support: IE <10 only
				// New HTML5 attribute values (e.g., "search") appear with elem.type === "text"
				( ( attr = elem.getAttribute( "type" ) ) == null ||
					attr.toLowerCase() === "text" );
		},

		// Position-in-collection
		"first": createPositionalPseudo( function() {
			return [ 0 ];
		} ),

		"last": createPositionalPseudo( function( _matchIndexes, length ) {
			return [ length - 1 ];
		} ),

		"eq": createPositionalPseudo( function( _matchIndexes, length, argument ) {
			return [ argument < 0 ? argument + length : argument ];
		} ),

		"even": createPositionalPseudo( function( matchIndexes, length ) {
			var i = 0;
			for ( ; i < length; i += 2 ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		} ),

		"odd": createPositionalPseudo( function( matchIndexes, length ) {
			var i = 1;
			for ( ; i < length; i += 2 ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		} ),

		"lt": createPositionalPseudo( function( matchIndexes, length, argument ) {
			var i = argument < 0 ?
				argument + length :
				argument > length ?
					length :
					argument;
			for ( ; --i >= 0; ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		} ),

		"gt": createPositionalPseudo( function( matchIndexes, length, argument ) {
			var i = argument < 0 ? argument + length : argument;
			for ( ; ++i < length; ) {
				matchIndexes.push( i );
			}
			return matchIndexes;
		} )
	}
};

Expr.pseudos[ "nth" ] = Expr.pseudos[ "eq" ];

// Add button/input type pseudos
for ( i in { radio: true, checkbox: true, file: true, password: true, image: true } ) {
	Expr.pseudos[ i ] = createInputPseudo( i );
}
for ( i in { submit: true, reset: true } ) {
	Expr.pseudos[ i ] = createButtonPseudo( i );
}

// Easy API for creating new setFilters
function setFilters() {}
setFilters.prototype = Expr.filters = Expr.pseudos;
Expr.setFilters = new setFilters();

tokenize = Sizzle.tokenize = function( selector, parseOnly ) {
	var matched, match, tokens, type,
		soFar, groups, preFilters,
		cached = tokenCache[ selector + " " ];

	if ( cached ) {
		return parseOnly ? 0 : cached.slice( 0 );
	}

	soFar = selector;
	groups = [];
	preFilters = Expr.preFilter;

	while ( soFar ) {

		// Comma and first run
		if ( !matched || ( match = rcomma.exec( soFar ) ) ) {
			if ( match ) {

				// Don't consume trailing commas as valid
				soFar = soFar.slice( match[ 0 ].length ) || soFar;
			}
			groups.push( ( tokens = [] ) );
		}

		matched = false;

		// Combinators
		if ( ( match = rleadingCombinator.exec( soFar ) ) ) {
			matched = match.shift();
			tokens.push( {
				value: matched,

				// Cast descendant combinators to space
				type: match[ 0 ].replace( rtrim, " " )
			} );
			soFar = soFar.slice( matched.length );
		}

		// Filters
		for ( type in Expr.filter ) {
			if ( ( match = matchExpr[ type ].exec( soFar ) ) && ( !preFilters[ type ] ||
				( match = preFilters[ type ]( match ) ) ) ) {
				matched = match.shift();
				tokens.push( {
					value: matched,
					type: type,
					matches: match
				} );
				soFar = soFar.slice( matched.length );
			}
		}

		if ( !matched ) {
			break;
		}
	}

	// Return the length of the invalid excess
	// if we're just parsing
	// Otherwise, throw an error or return tokens
	return parseOnly ?
		soFar.length :
		soFar ?
			Sizzle.error( selector ) :

			// Cache the tokens
			tokenCache( selector, groups ).slice( 0 );
};

function toSelector( tokens ) {
	var i = 0,
		len = tokens.length,
		selector = "";
	for ( ; i < len; i++ ) {
		selector += tokens[ i ].value;
	}
	return selector;
}

function addCombinator( matcher, combinator, base ) {
	var dir = combinator.dir,
		skip = combinator.next,
		key = skip || dir,
		checkNonElements = base && key === "parentNode",
		doneName = done++;

	return combinator.first ?

		// Check against closest ancestor/preceding element
		function( elem, context, xml ) {
			while ( ( elem = elem[ dir ] ) ) {
				if ( elem.nodeType === 1 || checkNonElements ) {
					return matcher( elem, context, xml );
				}
			}
			return false;
		} :

		// Check against all ancestor/preceding elements
		function( elem, context, xml ) {
			var oldCache, uniqueCache, outerCache,
				newCache = [ dirruns, doneName ];

			// We can't set arbitrary data on XML nodes, so they don't benefit from combinator caching
			if ( xml ) {
				while ( ( elem = elem[ dir ] ) ) {
					if ( elem.nodeType === 1 || checkNonElements ) {
						if ( matcher( elem, context, xml ) ) {
							return true;
						}
					}
				}
			} else {
				while ( ( elem = elem[ dir ] ) ) {
					if ( elem.nodeType === 1 || checkNonElements ) {
						outerCache = elem[ expando ] || ( elem[ expando ] = {} );

						// Support: IE <9 only
						// Defend against cloned attroperties (jQuery gh-1709)
						uniqueCache = outerCache[ elem.uniqueID ] ||
							( outerCache[ elem.uniqueID ] = {} );

						if ( skip && skip === elem.nodeName.toLowerCase() ) {
							elem = elem[ dir ] || elem;
						} else if ( ( oldCache = uniqueCache[ key ] ) &&
							oldCache[ 0 ] === dirruns && oldCache[ 1 ] === doneName ) {

							// Assign to newCache so results back-propagate to previous elements
							return ( newCache[ 2 ] = oldCache[ 2 ] );
						} else {

							// Reuse newcache so results back-propagate to previous elements
							uniqueCache[ key ] = newCache;

							// A match means we're done; a fail means we have to keep checking
							if ( ( newCache[ 2 ] = matcher( elem, context, xml ) ) ) {
								return true;
							}
						}
					}
				}
			}
			return false;
		};
}

function elementMatcher( matchers ) {
	return matchers.length > 1 ?
		function( elem, context, xml ) {
			var i = matchers.length;
			while ( i-- ) {
				if ( !matchers[ i ]( elem, context, xml ) ) {
					return false;
				}
			}
			return true;
		} :
		matchers[ 0 ];
}

function multipleContexts( selector, contexts, results ) {
	var i = 0,
		len = contexts.length;
	for ( ; i < len; i++ ) {
		Sizzle( selector, contexts[ i ], results );
	}
	return results;
}

function condense( unmatched, map, filter, context, xml ) {
	var elem,
		newUnmatched = [],
		i = 0,
		len = unmatched.length,
		mapped = map != null;

	for ( ; i < len; i++ ) {
		if ( ( elem = unmatched[ i ] ) ) {
			if ( !filter || filter( elem, context, xml ) ) {
				newUnmatched.push( elem );
				if ( mapped ) {
					map.push( i );
				}
			}
		}
	}

	return newUnmatched;
}

function setMatcher( preFilter, selector, matcher, postFilter, postFinder, postSelector ) {
	if ( postFilter && !postFilter[ expando ] ) {
		postFilter = setMatcher( postFilter );
	}
	if ( postFinder && !postFinder[ expando ] ) {
		postFinder = setMatcher( postFinder, postSelector );
	}
	return markFunction( function( seed, results, context, xml ) {
		var temp, i, elem,
			preMap = [],
			postMap = [],
			preexisting = results.length,

			// Get initial elements from seed or context
			elems = seed || multipleContexts(
				selector || "*",
				context.nodeType ? [ context ] : context,
				[]
			),

			// Prefilter to get matcher input, preserving a map for seed-results synchronization
			matcherIn = preFilter && ( seed || !selector ) ?
				condense( elems, preMap, preFilter, context, xml ) :
				elems,

			matcherOut = matcher ?

				// If we have a postFinder, or filtered seed, or non-seed postFilter or preexisting results,
				postFinder || ( seed ? preFilter : preexisting || postFilter ) ?

					// ...intermediate processing is necessary
					[] :

					// ...otherwise use results directly
					results :
				matcherIn;

		// Find primary matches
		if ( matcher ) {
			matcher( matcherIn, matcherOut, context, xml );
		}

		// Apply postFilter
		if ( postFilter ) {
			temp = condense( matcherOut, postMap );
			postFilter( temp, [], context, xml );

			// Un-match failing elements by moving them back to matcherIn
			i = temp.length;
			while ( i-- ) {
				if ( ( elem = temp[ i ] ) ) {
					matcherOut[ postMap[ i ] ] = !( matcherIn[ postMap[ i ] ] = elem );
				}
			}
		}

		if ( seed ) {
			if ( postFinder || preFilter ) {
				if ( postFinder ) {

					// Get the final matcherOut by condensing this intermediate into postFinder contexts
					temp = [];
					i = matcherOut.length;
					while ( i-- ) {
						if ( ( elem = matcherOut[ i ] ) ) {

							// Restore matcherIn since elem is not yet a final match
							temp.push( ( matcherIn[ i ] = elem ) );
						}
					}
					postFinder( null, ( matcherOut = [] ), temp, xml );
				}

				// Move matched elements from seed to results to keep them synchronized
				i = matcherOut.length;
				while ( i-- ) {
					if ( ( elem = matcherOut[ i ] ) &&
						( temp = postFinder ? indexOf( seed, elem ) : preMap[ i ] ) > -1 ) {

						seed[ temp ] = !( results[ temp ] = elem );
					}
				}
			}

		// Add elements to results, through postFinder if defined
		} else {
			matcherOut = condense(
				matcherOut === results ?
					matcherOut.splice( preexisting, matcherOut.length ) :
					matcherOut
			);
			if ( postFinder ) {
				postFinder( null, results, matcherOut, xml );
			} else {
				push.apply( results, matcherOut );
			}
		}
	} );
}

function matcherFromTokens( tokens ) {
	var checkContext, matcher, j,
		len = tokens.length,
		leadingRelative = Expr.relative[ tokens[ 0 ].type ],
		implicitRelative = leadingRelative || Expr.relative[ " " ],
		i = leadingRelative ? 1 : 0,

		// The foundational matcher ensures that elements are reachable from top-level context(s)
		matchContext = addCombinator( function( elem ) {
			return elem === checkContext;
		}, implicitRelative, true ),
		matchAnyContext = addCombinator( function( elem ) {
			return indexOf( checkContext, elem ) > -1;
		}, implicitRelative, true ),
		matchers = [ function( elem, context, xml ) {
			var ret = ( !leadingRelative && ( xml || context !== outermostContext ) ) || (
				( checkContext = context ).nodeType ?
					matchContext( elem, context, xml ) :
					matchAnyContext( elem, context, xml ) );

			// Avoid hanging onto element (issue #299)
			checkContext = null;
			return ret;
		} ];

	for ( ; i < len; i++ ) {
		if ( ( matcher = Expr.relative[ tokens[ i ].type ] ) ) {
			matchers = [ addCombinator( elementMatcher( matchers ), matcher ) ];
		} else {
			matcher = Expr.filter[ tokens[ i ].type ].apply( null, tokens[ i ].matches );

			// Return special upon seeing a positional matcher
			if ( matcher[ expando ] ) {

				// Find the next relative operator (if any) for proper handling
				j = ++i;
				for ( ; j < len; j++ ) {
					if ( Expr.relative[ tokens[ j ].type ] ) {
						break;
					}
				}
				return setMatcher(
					i > 1 && elementMatcher( matchers ),
					i > 1 && toSelector(

					// If the preceding token was a descendant combinator, insert an implicit any-element `*`
					tokens
						.slice( 0, i - 1 )
						.concat( { value: tokens[ i - 2 ].type === " " ? "*" : "" } )
					).replace( rtrim, "$1" ),
					matcher,
					i < j && matcherFromTokens( tokens.slice( i, j ) ),
					j < len && matcherFromTokens( ( tokens = tokens.slice( j ) ) ),
					j < len && toSelector( tokens )
				);
			}
			matchers.push( matcher );
		}
	}

	return elementMatcher( matchers );
}

function matcherFromGroupMatchers( elementMatchers, setMatchers ) {
	var bySet = setMatchers.length > 0,
		byElement = elementMatchers.length > 0,
		superMatcher = function( seed, context, xml, results, outermost ) {
			var elem, j, matcher,
				matchedCount = 0,
				i = "0",
				unmatched = seed && [],
				setMatched = [],
				contextBackup = outermostContext,

				// We must always have either seed elements or outermost context
				elems = seed || byElement && Expr.find[ "TAG" ]( "*", outermost ),

				// Use integer dirruns iff this is the outermost matcher
				dirrunsUnique = ( dirruns += contextBackup == null ? 1 : Math.random() || 0.1 ),
				len = elems.length;

			if ( outermost ) {

				// Support: IE 11+, Edge 17 - 18+
				// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
				// two documents; shallow comparisons work.
				// eslint-disable-next-line eqeqeq
				outermostContext = context == document || context || outermost;
			}

			// Add elements passing elementMatchers directly to results
			// Support: IE<9, Safari
			// Tolerate NodeList properties (IE: "length"; Safari: <number>) matching elements by id
			for ( ; i !== len && ( elem = elems[ i ] ) != null; i++ ) {
				if ( byElement && elem ) {
					j = 0;

					// Support: IE 11+, Edge 17 - 18+
					// IE/Edge sometimes throw a "Permission denied" error when strict-comparing
					// two documents; shallow comparisons work.
					// eslint-disable-next-line eqeqeq
					if ( !context && elem.ownerDocument != document ) {
						setDocument( elem );
						xml = !documentIsHTML;
					}
					while ( ( matcher = elementMatchers[ j++ ] ) ) {
						if ( matcher( elem, context || document, xml ) ) {
							results.push( elem );
							break;
						}
					}
					if ( outermost ) {
						dirruns = dirrunsUnique;
					}
				}

				// Track unmatched elements for set filters
				if ( bySet ) {

					// They will have gone through all possible matchers
					if ( ( elem = !matcher && elem ) ) {
						matchedCount--;
					}

					// Lengthen the array for every element, matched or not
					if ( seed ) {
						unmatched.push( elem );
					}
				}
			}

			// `i` is now the count of elements visited above, and adding it to `matchedCount`
			// makes the latter nonnegative.
			matchedCount += i;

			// Apply set filters to unmatched elements
			// NOTE: This can be skipped if there are no unmatched elements (i.e., `matchedCount`
			// equals `i`), unless we didn't visit _any_ elements in the above loop because we have
			// no element matchers and no seed.
			// Incrementing an initially-string "0" `i` allows `i` to remain a string only in that
			// case, which will result in a "00" `matchedCount` that differs from `i` but is also
			// numerically zero.
			if ( bySet && i !== matchedCount ) {
				j = 0;
				while ( ( matcher = setMatchers[ j++ ] ) ) {
					matcher( unmatched, setMatched, context, xml );
				}

				if ( seed ) {

					// Reintegrate element matches to eliminate the need for sorting
					if ( matchedCount > 0 ) {
						while ( i-- ) {
							if ( !( unmatched[ i ] || setMatched[ i ] ) ) {
								setMatched[ i ] = pop.call( results );
							}
						}
					}

					// Discard index placeholder values to get only actual matches
					setMatched = condense( setMatched );
				}

				// Add matches to results
				push.apply( results, setMatched );

				// Seedless set matches succeeding multiple successful matchers stipulate sorting
				if ( outermost && !seed && setMatched.length > 0 &&
					( matchedCount + setMatchers.length ) > 1 ) {

					Sizzle.uniqueSort( results );
				}
			}

			// Override manipulation of globals by nested matchers
			if ( outermost ) {
				dirruns = dirrunsUnique;
				outermostContext = contextBackup;
			}

			return unmatched;
		};

	return bySet ?
		markFunction( superMatcher ) :
		superMatcher;
}

compile = Sizzle.compile = function( selector, match /* Internal Use Only */ ) {
	var i,
		setMatchers = [],
		elementMatchers = [],
		cached = compilerCache[ selector + " " ];

	if ( !cached ) {

		// Generate a function of recursive functions that can be used to check each element
		if ( !match ) {
			match = tokenize( selector );
		}
		i = match.length;
		while ( i-- ) {
			cached = matcherFromTokens( match[ i ] );
			if ( cached[ expando ] ) {
				setMatchers.push( cached );
			} else {
				elementMatchers.push( cached );
			}
		}

		// Cache the compiled function
		cached = compilerCache(
			selector,
			matcherFromGroupMatchers( elementMatchers, setMatchers )
		);

		// Save selector and tokenization
		cached.selector = selector;
	}
	return cached;
};

/**
 * A low-level selection function that works with Sizzle's compiled
 *  selector functions
 * @param {String|Function} selector A selector or a pre-compiled
 *  selector function built with Sizzle.compile
 * @param {Element} context
 * @param {Array} [results]
 * @param {Array} [seed] A set of elements to match against
 */
select = Sizzle.select = function( selector, context, results, seed ) {
	var i, tokens, token, type, find,
		compiled = typeof selector === "function" && selector,
		match = !seed && tokenize( ( selector = compiled.selector || selector ) );

	results = results || [];

	// Try to minimize operations if there is only one selector in the list and no seed
	// (the latter of which guarantees us context)
	if ( match.length === 1 ) {

		// Reduce context if the leading compound selector is an ID
		tokens = match[ 0 ] = match[ 0 ].slice( 0 );
		if ( tokens.length > 2 && ( token = tokens[ 0 ] ).type === "ID" &&
			context.nodeType === 9 && documentIsHTML && Expr.relative[ tokens[ 1 ].type ] ) {

			context = ( Expr.find[ "ID" ]( token.matches[ 0 ]
				.replace( runescape, funescape ), context ) || [] )[ 0 ];
			if ( !context ) {
				return results;

			// Precompiled matchers will still verify ancestry, so step up a level
			} else if ( compiled ) {
				context = context.parentNode;
			}

			selector = selector.slice( tokens.shift().value.length );
		}

		// Fetch a seed set for right-to-left matching
		i = matchExpr[ "needsContext" ].test( selector ) ? 0 : tokens.length;
		while ( i-- ) {
			token = tokens[ i ];

			// Abort if we hit a combinator
			if ( Expr.relative[ ( type = token.type ) ] ) {
				break;
			}
			if ( ( find = Expr.find[ type ] ) ) {

				// Search, expanding context for leading sibling combinators
				if ( ( seed = find(
					token.matches[ 0 ].replace( runescape, funescape ),
					rsibling.test( tokens[ 0 ].type ) && testContext( context.parentNode ) ||
						context
				) ) ) {

					// If seed is empty or no tokens remain, we can return early
					tokens.splice( i, 1 );
					selector = seed.length && toSelector( tokens );
					if ( !selector ) {
						push.apply( results, seed );
						return results;
					}

					break;
				}
			}
		}
	}

	// Compile and execute a filtering function if one is not provided
	// Provide `match` to avoid retokenization if we modified the selector above
	( compiled || compile( selector, match ) )(
		seed,
		context,
		!documentIsHTML,
		results,
		!context || rsibling.test( selector ) && testContext( context.parentNode ) || context
	);
	return results;
};

// One-time assignments

// Sort stability
support.sortStable = expando.split( "" ).sort( sortOrder ).join( "" ) === expando;

// Support: Chrome 14-35+
// Always assume duplicates if they aren't passed to the comparison function
support.detectDuplicates = !!hasDuplicate;

// Initialize against the default document
setDocument();

// Support: Webkit<537.32 - Safari 6.0.3/Chrome 25 (fixed in Chrome 27)
// Detached nodes confoundingly follow *each other*
support.sortDetached = assert( function( el ) {

	// Should return 1, but returns 4 (following)
	return el.compareDocumentPosition( document.createElement( "fieldset" ) ) & 1;
} );

// Support: IE<8
// Prevent attribute/property "interpolation"
// https://msdn.microsoft.com/en-us/library/ms536429%28VS.85%29.aspx
if ( !assert( function( el ) {
	el.innerHTML = "<a href='#'></a>";
	return el.firstChild.getAttribute( "href" ) === "#";
} ) ) {
	addHandle( "type|href|height|width", function( elem, name, isXML ) {
		if ( !isXML ) {
			return elem.getAttribute( name, name.toLowerCase() === "type" ? 1 : 2 );
		}
	} );
}

// Support: IE<9
// Use defaultValue in place of getAttribute("value")
if ( !support.attributes || !assert( function( el ) {
	el.innerHTML = "<input/>";
	el.firstChild.setAttribute( "value", "" );
	return el.firstChild.getAttribute( "value" ) === "";
} ) ) {
	addHandle( "value", function( elem, _name, isXML ) {
		if ( !isXML && elem.nodeName.toLowerCase() === "input" ) {
			return elem.defaultValue;
		}
	} );
}

// Support: IE<9
// Use getAttributeNode to fetch booleans when getAttribute lies
if ( !assert( function( el ) {
	return el.getAttribute( "disabled" ) == null;
} ) ) {
	addHandle( booleans, function( elem, name, isXML ) {
		var val;
		if ( !isXML ) {
			return elem[ name ] === true ? name.toLowerCase() :
				( val = elem.getAttributeNode( name ) ) && val.specified ?
					val.value :
					null;
		}
	} );
}

return Sizzle;

} )( window );



jQuery.find = Sizzle;
jQuery.expr = Sizzle.selectors;

// Deprecated
jQuery.expr[ ":" ] = jQuery.expr.pseudos;
jQuery.uniqueSort = jQuery.unique = Sizzle.uniqueSort;
jQuery.text = Sizzle.getText;
jQuery.isXMLDoc = Sizzle.isXML;
jQuery.contains = Sizzle.contains;
jQuery.escapeSelector = Sizzle.escape;




var dir = function( elem, dir, until ) {
	var matched = [],
		truncate = until !== undefined;

	while ( ( elem = elem[ dir ] ) && elem.nodeType !== 9 ) {
		if ( elem.nodeType === 1 ) {
			if ( truncate && jQuery( elem ).is( until ) ) {
				break;
			}
			matched.push( elem );
		}
	}
	return matched;
};


var siblings = function( n, elem ) {
	var matched = [];

	for ( ; n; n = n.nextSibling ) {
		if ( n.nodeType === 1 && n !== elem ) {
			matched.push( n );
		}
	}

	return matched;
};


var rneedsContext = jQuery.expr.match.needsContext;



function nodeName( elem, name ) {

	return elem.nodeName && elem.nodeName.toLowerCase() === name.toLowerCase();

}
var rsingleTag = ( /^<([a-z][^\/\0>:\x20\t\r\n\f]*)[\x20\t\r\n\f]*\/?>(?:<\/\1>|)$/i );



// Implement the identical functionality for filter and not
function winnow( elements, qualifier, not ) {
	if ( isFunction( qualifier ) ) {
		return jQuery.grep( elements, function( elem, i ) {
			return !!qualifier.call( elem, i, elem ) !== not;
		} );
	}

	// Single element
	if ( qualifier.nodeType ) {
		return jQuery.grep( elements, function( elem ) {
			return ( elem === qualifier ) !== not;
		} );
	}

	// Arraylike of elements (jQuery, arguments, Array)
	if ( typeof qualifier !== "string" ) {
		return jQuery.grep( elements, function( elem ) {
			return ( indexOf.call( qualifier, elem ) > -1 ) !== not;
		} );
	}

	// Filtered directly for both simple and complex selectors
	return jQuery.filter( qualifier, elements, not );
}

jQuery.filter = function( expr, elems, not ) {
	var elem = elems[ 0 ];

	if ( not ) {
		expr = ":not(" + expr + ")";
	}

	if ( elems.length === 1 && elem.nodeType === 1 ) {
		return jQuery.find.matchesSelector( elem, expr ) ? [ elem ] : [];
	}

	return jQuery.find.matches( expr, jQuery.grep( elems, function( elem ) {
		return elem.nodeType === 1;
	} ) );
};

jQuery.fn.extend( {
	find: function( selector ) {
		var i, ret,
			len = this.length,
			self = this;

		if ( typeof selector !== "string" ) {
			return this.pushStack( jQuery( selector ).filter( function() {
				for ( i = 0; i < len; i++ ) {
					if ( jQuery.contains( self[ i ], this ) ) {
						return true;
					}
				}
			} ) );
		}

		ret = this.pushStack( [] );

		for ( i = 0; i < len; i++ ) {
			jQuery.find( selector, self[ i ], ret );
		}

		return len > 1 ? jQuery.uniqueSort( ret ) : ret;
	},
	filter: function( selector ) {
		return this.pushStack( winnow( this, selector || [], false ) );
	},
	not: function( selector ) {
		return this.pushStack( winnow( this, selector || [], true ) );
	},
	is: function( selector ) {
		return !!winnow(
			this,

			// If this is a positional/relative selector, check membership in the returned set
			// so $("p:first").is("p:last") won't return true for a doc with two "p".
			typeof selector === "string" && rneedsContext.test( selector ) ?
				jQuery( selector ) :
				selector || [],
			false
		).length;
	}
} );


// Initialize a jQuery object


// A central reference to the root jQuery(document)
var rootjQuery,

	// A simple way to check for HTML strings
	// Prioritize #id over <tag> to avoid XSS via location.hash (trac-9521)
	// Strict HTML recognition (trac-11290: must start with <)
	// Shortcut simple #id case for speed
	rquickExpr = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/,

	init = jQuery.fn.init = function( selector, context, root ) {
		var match, elem;

		// HANDLE: $(""), $(null), $(undefined), $(false)
		if ( !selector ) {
			return this;
		}

		// Method init() accepts an alternate rootjQuery
		// so migrate can support jQuery.sub (gh-2101)
		root = root || rootjQuery;

		// Handle HTML strings
		if ( typeof selector === "string" ) {
			if ( selector[ 0 ] === "<" &&
				selector[ selector.length - 1 ] === ">" &&
				selector.length >= 3 ) {

				// Assume that strings that start and end with <> are HTML and skip the regex check
				match = [ null, selector, null ];

			} else {
				match = rquickExpr.exec( selector );
			}

			// Match html or make sure no context is specified for #id
			if ( match && ( match[ 1 ] || !context ) ) {

				// HANDLE: $(html) -> $(array)
				if ( match[ 1 ] ) {
					context = context instanceof jQuery ? context[ 0 ] : context;

					// Option to run scripts is true for back-compat
					// Intentionally let the error be thrown if parseHTML is not present
					jQuery.merge( this, jQuery.parseHTML(
						match[ 1 ],
						context && context.nodeType ? context.ownerDocument || context : document,
						true
					) );

					// HANDLE: $(html, props)
					if ( rsingleTag.test( match[ 1 ] ) && jQuery.isPlainObject( context ) ) {
						for ( match in context ) {

							// Properties of context are called as methods if possible
							if ( isFunction( this[ match ] ) ) {
								this[ match ]( context[ match ] );

							// ...and otherwise set as attributes
							} else {
								this.attr( match, context[ match ] );
							}
						}
					}

					return this;

				// HANDLE: $(#id)
				} else {
					elem = document.getElementById( match[ 2 ] );

					if ( elem ) {

						// Inject the element directly into the jQuery object
						this[ 0 ] = elem;
						this.length = 1;
					}
					return this;
				}

			// HANDLE: $(expr, $(...))
			} else if ( !context || context.jquery ) {
				return ( context || root ).find( selector );

			// HANDLE: $(expr, context)
			// (which is just equivalent to: $(context).find(expr)
			} else {
				return this.constructor( context ).find( selector );
			}

		// HANDLE: $(DOMElement)
		} else if ( selector.nodeType ) {
			this[ 0 ] = selector;
			this.length = 1;
			return this;

		// HANDLE: $(function)
		// Shortcut for document ready
		} else if ( isFunction( selector ) ) {
			return root.ready !== undefined ?
				root.ready( selector ) :

				// Execute immediately if ready is not present
				selector( jQuery );
		}

		return jQuery.makeArray( selector, this );
	};

// Give the init function the jQuery prototype for later instantiation
init.prototype = jQuery.fn;

// Initialize central reference
rootjQuery = jQuery( document );


var rparentsprev = /^(?:parents|prev(?:Until|All))/,

	// Methods guaranteed to produce a unique set when starting from a unique set
	guaranteedUnique = {
		children: true,
		contents: true,
		next: true,
		prev: true
	};

jQuery.fn.extend( {
	has: function( target ) {
		var targets = jQuery( target, this ),
			l = targets.length;

		return this.filter( function() {
			var i = 0;
			for ( ; i < l; i++ ) {
				if ( jQuery.contains( this, targets[ i ] ) ) {
					return true;
				}
			}
		} );
	},

	closest: function( selectors, context ) {
		var cur,
			i = 0,
			l = this.length,
			matched = [],
			targets = typeof selectors !== "string" && jQuery( selectors );

		// Positional selectors never match, since there's no _selection_ context
		if ( !rneedsContext.test( selectors ) ) {
			for ( ; i < l; i++ ) {
				for ( cur = this[ i ]; cur && cur !== context; cur = cur.parentNode ) {

					// Always skip document fragments
					if ( cur.nodeType < 11 && ( targets ?
						targets.index( cur ) > -1 :

						// Don't pass non-elements to Sizzle
						cur.nodeType === 1 &&
							jQuery.find.matchesSelector( cur, selectors ) ) ) {

						matched.push( cur );
						break;
					}
				}
			}
		}

		return this.pushStack( matched.length > 1 ? jQuery.uniqueSort( matched ) : matched );
	},

	// Determine the position of an element within the set
	index: function( elem ) {

		// No argument, return index in parent
		if ( !elem ) {
			return ( this[ 0 ] && this[ 0 ].parentNode ) ? this.first().prevAll().length : -1;
		}

		// Index in selector
		if ( typeof elem === "string" ) {
			return indexOf.call( jQuery( elem ), this[ 0 ] );
		}

		// Locate the position of the desired element
		return indexOf.call( this,

			// If it receives a jQuery object, the first element is used
			elem.jquery ? elem[ 0 ] : elem
		);
	},

	add: function( selector, context ) {
		return this.pushStack(
			jQuery.uniqueSort(
				jQuery.merge( this.get(), jQuery( selector, context ) )
			)
		);
	},

	addBack: function( selector ) {
		return this.add( selector == null ?
			this.prevObject : this.prevObject.filter( selector )
		);
	}
} );

function sibling( cur, dir ) {
	while ( ( cur = cur[ dir ] ) && cur.nodeType !== 1 ) {}
	return cur;
}

jQuery.each( {
	parent: function( elem ) {
		var parent = elem.parentNode;
		return parent && parent.nodeType !== 11 ? parent : null;
	},
	parents: function( elem ) {
		return dir( elem, "parentNode" );
	},
	parentsUntil: function( elem, _i, until ) {
		return dir( elem, "parentNode", until );
	},
	next: function( elem ) {
		return sibling( elem, "nextSibling" );
	},
	prev: function( elem ) {
		return sibling( elem, "previousSibling" );
	},
	nextAll: function( elem ) {
		return dir( elem, "nextSibling" );
	},
	prevAll: function( elem ) {
		return dir( elem, "previousSibling" );
	},
	nextUntil: function( elem, _i, until ) {
		return dir( elem, "nextSibling", until );
	},
	prevUntil: function( elem, _i, until ) {
		return dir( elem, "previousSibling", until );
	},
	siblings: function( elem ) {
		return siblings( ( elem.parentNode || {} ).firstChild, elem );
	},
	children: function( elem ) {
		return siblings( elem.firstChild );
	},
	contents: function( elem ) {
		if ( elem.contentDocument != null &&

			// Support: IE 11+
			// <object> elements with no `data` attribute has an object
			// `contentDocument` with a `null` prototype.
			getProto( elem.contentDocument ) ) {

			return elem.contentDocument;
		}

		// Support: IE 9 - 11 only, iOS 7 only, Android Browser <=4.3 only
		// Treat the template element as a regular one in browsers that
		// don't support it.
		if ( nodeName( elem, "template" ) ) {
			elem = elem.content || elem;
		}

		return jQuery.merge( [], elem.childNodes );
	}
}, function( name, fn ) {
	jQuery.fn[ name ] = function( until, selector ) {
		var matched = jQuery.map( this, fn, until );

		if ( name.slice( -5 ) !== "Until" ) {
			selector = until;
		}

		if ( selector && typeof selector === "string" ) {
			matched = jQuery.filter( selector, matched );
		}

		if ( this.length > 1 ) {

			// Remove duplicates
			if ( !guaranteedUnique[ name ] ) {
				jQuery.uniqueSort( matched );
			}

			// Reverse order for parents* and prev-derivatives
			if ( rparentsprev.test( name ) ) {
				matched.reverse();
			}
		}

		return this.pushStack( matched );
	};
} );
var rnothtmlwhite = ( /[^\x20\t\r\n\f]+/g );



// Convert String-formatted options into Object-formatted ones
function createOptions( options ) {
	var object = {};
	jQuery.each( options.match( rnothtmlwhite ) || [], function( _, flag ) {
		object[ flag ] = true;
	} );
	return object;
}

/*
 * Create a callback list using the following parameters:
 *
 *	options: an optional list of space-separated options that will change how
 *			the callback list behaves or a more traditional option object
 *
 * By default a callback list will act like an event callback list and can be
 * "fired" multiple times.
 *
 * Possible options:
 *
 *	once:			will ensure the callback list can only be fired once (like a Deferred)
 *
 *	memory:			will keep track of previous values and will call any callback added
 *					after the list has been fired right away with the latest "memorized"
 *					values (like a Deferred)
 *
 *	unique:			will ensure a callback can only be added once (no duplicate in the list)
 *
 *	stopOnFalse:	interrupt callings when a callback returns false
 *
 */
jQuery.Callbacks = function( options ) {

	// Convert options from String-formatted to Object-formatted if needed
	// (we check in cache first)
	options = typeof options === "string" ?
		createOptions( options ) :
		jQuery.extend( {}, options );

	var // Flag to know if list is currently firing
		firing,

		// Last fire value for non-forgettable lists
		memory,

		// Flag to know if list was already fired
		fired,

		// Flag to prevent firing
		locked,

		// Actual callback list
		list = [],

		// Queue of execution data for repeatable lists
		queue = [],

		// Index of currently firing callback (modified by add/remove as needed)
		firingIndex = -1,

		// Fire callbacks
		fire = function() {

			// Enforce single-firing
			locked = locked || options.once;

			// Execute callbacks for all pending executions,
			// respecting firingIndex overrides and runtime changes
			fired = firing = true;
			for ( ; queue.length; firingIndex = -1 ) {
				memory = queue.shift();
				while ( ++firingIndex < list.length ) {

					// Run callback and check for early termination
					if ( list[ firingIndex ].apply( memory[ 0 ], memory[ 1 ] ) === false &&
						options.stopOnFalse ) {

						// Jump to end and forget the data so .add doesn't re-fire
						firingIndex = list.length;
						memory = false;
					}
				}
			}

			// Forget the data if we're done with it
			if ( !options.memory ) {
				memory = false;
			}

			firing = false;

			// Clean up if we're done firing for good
			if ( locked ) {

				// Keep an empty list if we have data for future add calls
				if ( memory ) {
					list = [];

				// Otherwise, this object is spent
				} else {
					list = "";
				}
			}
		},

		// Actual Callbacks object
		self = {

			// Add a callback or a collection of callbacks to the list
			add: function() {
				if ( list ) {

					// If we have memory from a past run, we should fire after adding
					if ( memory && !firing ) {
						firingIndex = list.length - 1;
						queue.push( memory );
					}

					( function add( args ) {
						jQuery.each( args, function( _, arg ) {
							if ( isFunction( arg ) ) {
								if ( !options.unique || !self.has( arg ) ) {
									list.push( arg );
								}
							} else if ( arg && arg.length && toType( arg ) !== "string" ) {

								// Inspect recursively
								add( arg );
							}
						} );
					} )( arguments );

					if ( memory && !firing ) {
						fire();
					}
				}
				return this;
			},

			// Remove a callback from the list
			remove: function() {
				jQuery.each( arguments, function( _, arg ) {
					var index;
					while ( ( index = jQuery.inArray( arg, list, index ) ) > -1 ) {
						list.splice( index, 1 );

						// Handle firing indexes
						if ( index <= firingIndex ) {
							firingIndex--;
						}
					}
				} );
				return this;
			},

			// Check if a given callback is in the list.
			// If no argument is given, return whether or not list has callbacks attached.
			has: function( fn ) {
				return fn ?
					jQuery.inArray( fn, list ) > -1 :
					list.length > 0;
			},

			// Remove all callbacks from the list
			empty: function() {
				if ( list ) {
					list = [];
				}
				return this;
			},

			// Disable .fire and .add
			// Abort any current/pending executions
			// Clear all callbacks and values
			disable: function() {
				locked = queue = [];
				list = memory = "";
				return this;
			},
			disabled: function() {
				return !list;
			},

			// Disable .fire
			// Also disable .add unless we have memory (since it would have no effect)
			// Abort any pending executions
			lock: function() {
				locked = queue = [];
				if ( !memory && !firing ) {
					list = memory = "";
				}
				return this;
			},
			locked: function() {
				return !!locked;
			},

			// Call all callbacks with the given context and arguments
			fireWith: function( context, args ) {
				if ( !locked ) {
					args = args || [];
					args = [ context, args.slice ? args.slice() : args ];
					queue.push( args );
					if ( !firing ) {
						fire();
					}
				}
				return this;
			},

			// Call all the callbacks with the given arguments
			fire: function() {
				self.fireWith( this, arguments );
				return this;
			},

			// To know if the callbacks have already been called at least once
			fired: function() {
				return !!fired;
			}
		};

	return self;
};


function Identity( v ) {
	return v;
}
function Thrower( ex ) {
	throw ex;
}

function adoptValue( value, resolve, reject, noValue ) {
	var method;

	try {

		// Check for promise aspect first to privilege synchronous behavior
		if ( value && isFunction( ( method = value.promise ) ) ) {
			method.call( value ).done( resolve ).fail( reject );

		// Other thenables
		} else if ( value && isFunction( ( method = value.then ) ) ) {
			method.call( value, resolve, reject );

		// Other non-thenables
		} else {

			// Control `resolve` arguments by letting Array#slice cast boolean `noValue` to integer:
			// * false: [ value ].slice( 0 ) => resolve( value )
			// * true: [ value ].slice( 1 ) => resolve()
			resolve.apply( undefined, [ value ].slice( noValue ) );
		}

	// For Promises/A+, convert exceptions into rejections
	// Since jQuery.when doesn't unwrap thenables, we can skip the extra checks appearing in
	// Deferred#then to conditionally suppress rejection.
	} catch ( value ) {

		// Support: Android 4.0 only
		// Strict mode functions invoked without .call/.apply get global-object context
		reject.apply( undefined, [ value ] );
	}
}

jQuery.extend( {

	Deferred: function( func ) {
		var tuples = [

				// action, add listener, callbacks,
				// ... .then handlers, argument index, [final state]
				[ "notify", "progress", jQuery.Callbacks( "memory" ),
					jQuery.Callbacks( "memory" ), 2 ],
				[ "resolve", "done", jQuery.Callbacks( "once memory" ),
					jQuery.Callbacks( "once memory" ), 0, "resolved" ],
				[ "reject", "fail", jQuery.Callbacks( "once memory" ),
					jQuery.Callbacks( "once memory" ), 1, "rejected" ]
			],
			state = "pending",
			promise = {
				state: function() {
					return state;
				},
				always: function() {
					deferred.done( arguments ).fail( arguments );
					return this;
				},
				"catch": function( fn ) {
					return promise.then( null, fn );
				},

				// Keep pipe for back-compat
				pipe: function( /* fnDone, fnFail, fnProgress */ ) {
					var fns = arguments;

					return jQuery.Deferred( function( newDefer ) {
						jQuery.each( tuples, function( _i, tuple ) {

							// Map tuples (progress, done, fail) to arguments (done, fail, progress)
							var fn = isFunction( fns[ tuple[ 4 ] ] ) && fns[ tuple[ 4 ] ];

							// deferred.progress(function() { bind to newDefer or newDefer.notify })
							// deferred.done(function() { bind to newDefer or newDefer.resolve })
							// deferred.fail(function() { bind to newDefer or newDefer.reject })
							deferred[ tuple[ 1 ] ]( function() {
								var returned = fn && fn.apply( this, arguments );
								if ( returned && isFunction( returned.promise ) ) {
									returned.promise()
										.progress( newDefer.notify )
										.done( newDefer.resolve )
										.fail( newDefer.reject );
								} else {
									newDefer[ tuple[ 0 ] + "With" ](
										this,
										fn ? [ returned ] : arguments
									);
								}
							} );
						} );
						fns = null;
					} ).promise();
				},
				then: function( onFulfilled, onRejected, onProgress ) {
					var maxDepth = 0;
					function resolve( depth, deferred, handler, special ) {
						return function() {
							var that = this,
								args = arguments,
								mightThrow = function() {
									var returned, then;

									// Support: Promises/A+ section 2.3.3.3.3
									// https://promisesaplus.com/#point-59
									// Ignore double-resolution attempts
									if ( depth < maxDepth ) {
										return;
									}

									returned = handler.apply( that, args );

									// Support: Promises/A+ section 2.3.1
									// https://promisesaplus.com/#point-48
									if ( returned === deferred.promise() ) {
										throw new TypeError( "Thenable self-resolution" );
									}

									// Support: Promises/A+ sections 2.3.3.1, 3.5
									// https://promisesaplus.com/#point-54
									// https://promisesaplus.com/#point-75
									// Retrieve `then` only once
									then = returned &&

										// Support: Promises/A+ section 2.3.4
										// https://promisesaplus.com/#point-64
										// Only check objects and functions for thenability
										( typeof returned === "object" ||
											typeof returned === "function" ) &&
										returned.then;

									// Handle a returned thenable
									if ( isFunction( then ) ) {

										// Special processors (notify) just wait for resolution
										if ( special ) {
											then.call(
												returned,
												resolve( maxDepth, deferred, Identity, special ),
												resolve( maxDepth, deferred, Thrower, special )
											);

										// Normal processors (resolve) also hook into progress
										} else {

											// ...and disregard older resolution values
											maxDepth++;

											then.call(
												returned,
												resolve( maxDepth, deferred, Identity, special ),
												resolve( maxDepth, deferred, Thrower, special ),
												resolve( maxDepth, deferred, Identity,
													deferred.notifyWith )
											);
										}

									// Handle all other returned values
									} else {

										// Only substitute handlers pass on context
										// and multiple values (non-spec behavior)
										if ( handler !== Identity ) {
											that = undefined;
											args = [ returned ];
										}

										// Process the value(s)
										// Default process is resolve
										( special || deferred.resolveWith )( that, args );
									}
								},

								// Only normal processors (resolve) catch and reject exceptions
								process = special ?
									mightThrow :
									function() {
										try {
											mightThrow();
										} catch ( e ) {

											if ( jQuery.Deferred.exceptionHook ) {
												jQuery.Deferred.exceptionHook( e,
													process.stackTrace );
											}

											// Support: Promises/A+ section 2.3.3.3.4.1
											// https://promisesaplus.com/#point-61
											// Ignore post-resolution exceptions
											if ( depth + 1 >= maxDepth ) {

												// Only substitute handlers pass on context
												// and multiple values (non-spec behavior)
												if ( handler !== Thrower ) {
													that = undefined;
													args = [ e ];
												}

												deferred.rejectWith( that, args );
											}
										}
									};

							// Support: Promises/A+ section 2.3.3.3.1
							// https://promisesaplus.com/#point-57
							// Re-resolve promises immediately to dodge false rejection from
							// subsequent errors
							if ( depth ) {
								process();
							} else {

								// Call an optional hook to record the stack, in case of exception
								// since it's otherwise lost when execution goes async
								if ( jQuery.Deferred.getStackHook ) {
									process.stackTrace = jQuery.Deferred.getStackHook();
								}
								window.setTimeout( process );
							}
						};
					}

					return jQuery.Deferred( function( newDefer ) {

						// progress_handlers.add( ... )
						tuples[ 0 ][ 3 ].add(
							resolve(
								0,
								newDefer,
								isFunction( onProgress ) ?
									onProgress :
									Identity,
								newDefer.notifyWith
							)
						);

						// fulfilled_handlers.add( ... )
						tuples[ 1 ][ 3 ].add(
							resolve(
								0,
								newDefer,
								isFunction( onFulfilled ) ?
									onFulfilled :
									Identity
							)
						);

						// rejected_handlers.add( ... )
						tuples[ 2 ][ 3 ].add(
							resolve(
								0,
								newDefer,
								isFunction( onRejected ) ?
									onRejected :
									Thrower
							)
						);
					} ).promise();
				},

				// Get a promise for this deferred
				// If obj is provided, the promise aspect is added to the object
				promise: function( obj ) {
					return obj != null ? jQuery.extend( obj, promise ) : promise;
				}
			},
			deferred = {};

		// Add list-specific methods
		jQuery.each( tuples, function( i, tuple ) {
			var list = tuple[ 2 ],
				stateString = tuple[ 5 ];

			// promise.progress = list.add
			// promise.done = list.add
			// promise.fail = list.add
			promise[ tuple[ 1 ] ] = list.add;

			// Handle state
			if ( stateString ) {
				list.add(
					function() {

						// state = "resolved" (i.e., fulfilled)
						// state = "rejected"
						state = stateString;
					},

					// rejected_callbacks.disable
					// fulfilled_callbacks.disable
					tuples[ 3 - i ][ 2 ].disable,

					// rejected_handlers.disable
					// fulfilled_handlers.disable
					tuples[ 3 - i ][ 3 ].disable,

					// progress_callbacks.lock
					tuples[ 0 ][ 2 ].lock,

					// progress_handlers.lock
					tuples[ 0 ][ 3 ].lock
				);
			}

			// progress_handlers.fire
			// fulfilled_handlers.fire
			// rejected_handlers.fire
			list.add( tuple[ 3 ].fire );

			// deferred.notify = function() { deferred.notifyWith(...) }
			// deferred.resolve = function() { deferred.resolveWith(...) }
			// deferred.reject = function() { deferred.rejectWith(...) }
			deferred[ tuple[ 0 ] ] = function() {
				deferred[ tuple[ 0 ] + "With" ]( this === deferred ? undefined : this, arguments );
				return this;
			};

			// deferred.notifyWith = list.fireWith
			// deferred.resolveWith = list.fireWith
			// deferred.rejectWith = list.fireWith
			deferred[ tuple[ 0 ] + "With" ] = list.fireWith;
		} );

		// Make the deferred a promise
		promise.promise( deferred );

		// Call given func if any
		if ( func ) {
			func.call( deferred, deferred );
		}

		// All done!
		return deferred;
	},

	// Deferred helper
	when: function( singleValue ) {
		var

			// count of uncompleted subordinates
			remaining = arguments.length,

			// count of unprocessed arguments
			i = remaining,

			// subordinate fulfillment data
			resolveContexts = Array( i ),
			resolveValues = slice.call( arguments ),

			// the primary Deferred
			primary = jQuery.Deferred(),

			// subordinate callback factory
			updateFunc = function( i ) {
				return function( value ) {
					resolveContexts[ i ] = this;
					resolveValues[ i ] = arguments.length > 1 ? slice.call( arguments ) : value;
					if ( !( --remaining ) ) {
						primary.resolveWith( resolveContexts, resolveValues );
					}
				};
			};

		// Single- and empty arguments are adopted like Promise.resolve
		if ( remaining <= 1 ) {
			adoptValue( singleValue, primary.done( updateFunc( i ) ).resolve, primary.reject,
				!remaining );

			// Use .then() to unwrap secondary thenables (cf. gh-3000)
			if ( primary.state() === "pending" ||
				isFunction( resolveValues[ i ] && resolveValues[ i ].then ) ) {

				return primary.then();
			}
		}

		// Multiple arguments are aggregated like Promise.all array elements
		while ( i-- ) {
			adoptValue( resolveValues[ i ], updateFunc( i ), primary.reject );
		}

		return primary.promise();
	}
} );


// These usually indicate a programmer mistake during development,
// warn about them ASAP rather than swallowing them by default.
var rerrorNames = /^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/;

jQuery.Deferred.exceptionHook = function( error, stack ) {

	// Support: IE 8 - 9 only
	// Console exists when dev tools are open, which can happen at any time
	if ( window.console && window.console.warn && error && rerrorNames.test( error.name ) ) {
		window.console.warn( "jQuery.Deferred exception: " + error.message, error.stack, stack );
	}
};




jQuery.readyException = function( error ) {
	window.setTimeout( function() {
		throw error;
	} );
};




// The deferred used on DOM ready
var readyList = jQuery.Deferred();

jQuery.fn.ready = function( fn ) {

	readyList
		.then( fn )

		// Wrap jQuery.readyException in a function so that the lookup
		// happens at the time of error handling instead of callback
		// registration.
		.catch( function( error ) {
			jQuery.readyException( error );
		} );

	return this;
};

jQuery.extend( {

	// Is the DOM ready to be used? Set to true once it occurs.
	isReady: false,

	// A counter to track how many items to wait for before
	// the ready event fires. See trac-6781
	readyWait: 1,

	// Handle when the DOM is ready
	ready: function( wait ) {

		// Abort if there are pending holds or we're already ready
		if ( wait === true ? --jQuery.readyWait : jQuery.isReady ) {
			return;
		}

		// Remember that the DOM is ready
		jQuery.isReady = true;

		// If a normal DOM Ready event fired, decrement, and wait if need be
		if ( wait !== true && --jQuery.readyWait > 0 ) {
			return;
		}

		// If there are functions bound, to execute
		readyList.resolveWith( document, [ jQuery ] );
	}
} );

jQuery.ready.then = readyList.then;

// The ready event handler and self cleanup method
function completed() {
	document.removeEventListener( "DOMContentLoaded", completed );
	window.removeEventListener( "load", completed );
	jQuery.ready();
}

// Catch cases where $(document).ready() is called
// after the browser event has already occurred.
// Support: IE <=9 - 10 only
// Older IE sometimes signals "interactive" too soon
if ( document.readyState === "complete" ||
	( document.readyState !== "loading" && !document.documentElement.doScroll ) ) {

	// Handle it asynchronously to allow scripts the opportunity to delay ready
	window.setTimeout( jQuery.ready );

} else {

	// Use the handy event callback
	document.addEventListener( "DOMContentLoaded", completed );

	// A fallback to window.onload, that will always work
	window.addEventListener( "load", completed );
}




// Multifunctional method to get and set values of a collection
// The value/s can optionally be executed if it's a function
var access = function( elems, fn, key, value, chainable, emptyGet, raw ) {
	var i = 0,
		len = elems.length,
		bulk = key == null;

	// Sets many values
	if ( toType( key ) === "object" ) {
		chainable = true;
		for ( i in key ) {
			access( elems, fn, i, key[ i ], true, emptyGet, raw );
		}

	// Sets one value
	} else if ( value !== undefined ) {
		chainable = true;

		if ( !isFunction( value ) ) {
			raw = true;
		}

		if ( bulk ) {

			// Bulk operations run against the entire set
			if ( raw ) {
				fn.call( elems, value );
				fn = null;

			// ...except when executing function values
			} else {
				bulk = fn;
				fn = function( elem, _key, value ) {
					return bulk.call( jQuery( elem ), value );
				};
			}
		}

		if ( fn ) {
			for ( ; i < len; i++ ) {
				fn(
					elems[ i ], key, raw ?
						value :
						value.call( elems[ i ], i, fn( elems[ i ], key ) )
				);
			}
		}
	}

	if ( chainable ) {
		return elems;
	}

	// Gets
	if ( bulk ) {
		return fn.call( elems );
	}

	return len ? fn( elems[ 0 ], key ) : emptyGet;
};


// Matches dashed string for camelizing
var rmsPrefix = /^-ms-/,
	rdashAlpha = /-([a-z])/g;

// Used by camelCase as callback to replace()
function fcamelCase( _all, letter ) {
	return letter.toUpperCase();
}

// Convert dashed to camelCase; used by the css and data modules
// Support: IE <=9 - 11, Edge 12 - 15
// Microsoft forgot to hump their vendor prefix (trac-9572)
function camelCase( string ) {
	return string.replace( rmsPrefix, "ms-" ).replace( rdashAlpha, fcamelCase );
}
var acceptData = function( owner ) {

	// Accepts only:
	//  - Node
	//    - Node.ELEMENT_NODE
	//    - Node.DOCUMENT_NODE
	//  - Object
	//    - Any
	return owner.nodeType === 1 || owner.nodeType === 9 || !( +owner.nodeType );
};




function Data() {
	this.expando = jQuery.expando + Data.uid++;
}

Data.uid = 1;

Data.prototype = {

	cache: function( owner ) {

		// Check if the owner object already has a cache
		var value = owner[ this.expando ];

		// If not, create one
		if ( !value ) {
			value = {};

			// We can accept data for non-element nodes in modern browsers,
			// but we should not, see trac-8335.
			// Always return an empty object.
			if ( acceptData( owner ) ) {

				// If it is a node unlikely to be stringify-ed or looped over
				// use plain assignment
				if ( owner.nodeType ) {
					owner[ this.expando ] = value;

				// Otherwise secure it in a non-enumerable property
				// configurable must be true to allow the property to be
				// deleted when data is removed
				} else {
					Object.defineProperty( owner, this.expando, {
						value: value,
						configurable: true
					} );
				}
			}
		}

		return value;
	},
	set: function( owner, data, value ) {
		var prop,
			cache = this.cache( owner );

		// Handle: [ owner, key, value ] args
		// Always use camelCase key (gh-2257)
		if ( typeof data === "string" ) {
			cache[ camelCase( data ) ] = value;

		// Handle: [ owner, { properties } ] args
		} else {

			// Copy the properties one-by-one to the cache object
			for ( prop in data ) {
				cache[ camelCase( prop ) ] = data[ prop ];
			}
		}
		return cache;
	},
	get: function( owner, key ) {
		return key === undefined ?
			this.cache( owner ) :

			// Always use camelCase key (gh-2257)
			owner[ this.expando ] && owner[ this.expando ][ camelCase( key ) ];
	},
	access: function( owner, key, value ) {

		// In cases where either:
		//
		//   1. No key was specified
		//   2. A string key was specified, but no value provided
		//
		// Take the "read" path and allow the get method to determine
		// which value to return, respectively either:
		//
		//   1. The entire cache object
		//   2. The data stored at the key
		//
		if ( key === undefined ||
				( ( key && typeof key === "string" ) && value === undefined ) ) {

			return this.get( owner, key );
		}

		// When the key is not a string, or both a key and value
		// are specified, set or extend (existing objects) with either:
		//
		//   1. An object of properties
		//   2. A key and value
		//
		this.set( owner, key, value );

		// Since the "set" path can have two possible entry points
		// return the expected data based on which path was taken[*]
		return value !== undefined ? value : key;
	},
	remove: function( owner, key ) {
		var i,
			cache = owner[ this.expando ];

		if ( cache === undefined ) {
			return;
		}

		if ( key !== undefined ) {

			// Support array or space separated string of keys
			if ( Array.isArray( key ) ) {

				// If key is an array of keys...
				// We always set camelCase keys, so remove that.
				key = key.map( camelCase );
			} else {
				key = camelCase( key );

				// If a key with the spaces exists, use it.
				// Otherwise, create an array by matching non-whitespace
				key = key in cache ?
					[ key ] :
					( key.match( rnothtmlwhite ) || [] );
			}

			i = key.length;

			while ( i-- ) {
				delete cache[ key[ i ] ];
			}
		}

		// Remove the expando if there's no more data
		if ( key === undefined || jQuery.isEmptyObject( cache ) ) {

			// Support: Chrome <=35 - 45
			// Webkit & Blink performance suffers when deleting properties
			// from DOM nodes, so set to undefined instead
			// https://bugs.chromium.org/p/chromium/issues/detail?id=378607 (bug restricted)
			if ( owner.nodeType ) {
				owner[ this.expando ] = undefined;
			} else {
				delete owner[ this.expando ];
			}
		}
	},
	hasData: function( owner ) {
		var cache = owner[ this.expando ];
		return cache !== undefined && !jQuery.isEmptyObject( cache );
	}
};
var dataPriv = new Data();

var dataUser = new Data();



//	Implementation Summary
//
//	1. Enforce API surface and semantic compatibility with 1.9.x branch
//	2. Improve the module's maintainability by reducing the storage
//		paths to a single mechanism.
//	3. Use the same single mechanism to support "private" and "user" data.
//	4. _Never_ expose "private" data to user code (TODO: Drop _data, _removeData)
//	5. Avoid exposing implementation details on user objects (eg. expando properties)
//	6. Provide a clear path for implementation upgrade to WeakMap in 2014

var rbrace = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
	rmultiDash = /[A-Z]/g;

function getData( data ) {
	if ( data === "true" ) {
		return true;
	}

	if ( data === "false" ) {
		return false;
	}

	if ( data === "null" ) {
		return null;
	}

	// Only convert to a number if it doesn't change the string
	if ( data === +data + "" ) {
		return +data;
	}

	if ( rbrace.test( data ) ) {
		return JSON.parse( data );
	}

	return data;
}

function dataAttr( elem, key, data ) {
	var name;

	// If nothing was found internally, try to fetch any
	// data from the HTML5 data-* attribute
	if ( data === undefined && elem.nodeType === 1 ) {
		name = "data-" + key.replace( rmultiDash, "-$&" ).toLowerCase();
		data = elem.getAttribute( name );

		if ( typeof data === "string" ) {
			try {
				data = getData( data );
			} catch ( e ) {}

			// Make sure we set the data so it isn't changed later
			dataUser.set( elem, key, data );
		} else {
			data = undefined;
		}
	}
	return data;
}

jQuery.extend( {
	hasData: function( elem ) {
		return dataUser.hasData( elem ) || dataPriv.hasData( elem );
	},

	data: function( elem, name, data ) {
		return dataUser.access( elem, name, data );
	},

	removeData: function( elem, name ) {
		dataUser.remove( elem, name );
	},

	// TODO: Now that all calls to _data and _removeData have been replaced
	// with direct calls to dataPriv methods, these can be deprecated.
	_data: function( elem, name, data ) {
		return dataPriv.access( elem, name, data );
	},

	_removeData: function( elem, name ) {
		dataPriv.remove( elem, name );
	}
} );

jQuery.fn.extend( {
	data: function( key, value ) {
		var i, name, data,
			elem = this[ 0 ],
			attrs = elem && elem.attributes;

		// Gets all values
		if ( key === undefined ) {
			if ( this.length ) {
				data = dataUser.get( elem );

				if ( elem.nodeType === 1 && !dataPriv.get( elem, "hasDataAttrs" ) ) {
					i = attrs.length;
					while ( i-- ) {

						// Support: IE 11 only
						// The attrs elements can be null (trac-14894)
						if ( attrs[ i ] ) {
							name = attrs[ i ].name;
							if ( name.indexOf( "data-" ) === 0 ) {
								name = camelCase( name.slice( 5 ) );
								dataAttr( elem, name, data[ name ] );
							}
						}
					}
					dataPriv.set( elem, "hasDataAttrs", true );
				}
			}

			return data;
		}

		// Sets multiple values
		if ( typeof key === "object" ) {
			return this.each( function() {
				dataUser.set( this, key );
			} );
		}

		return access( this, function( value ) {
			var data;

			// The calling jQuery object (element matches) is not empty
			// (and therefore has an element appears at this[ 0 ]) and the
			// `value` parameter was not undefined. An empty jQuery object
			// will result in `undefined` for elem = this[ 0 ] which will
			// throw an exception if an attempt to read a data cache is made.
			if ( elem && value === undefined ) {

				// Attempt to get data from the cache
				// The key will always be camelCased in Data
				data = dataUser.get( elem, key );
				if ( data !== undefined ) {
					return data;
				}

				// Attempt to "discover" the data in
				// HTML5 custom data-* attrs
				data = dataAttr( elem, key );
				if ( data !== undefined ) {
					return data;
				}

				// We tried really hard, but the data doesn't exist.
				return;
			}

			// Set the data...
			this.each( function() {

				// We always store the camelCased key
				dataUser.set( this, key, value );
			} );
		}, null, value, arguments.length > 1, null, true );
	},

	removeData: function( key ) {
		return this.each( function() {
			dataUser.remove( this, key );
		} );
	}
} );


jQuery.extend( {
	queue: function( elem, type, data ) {
		var queue;

		if ( elem ) {
			type = ( type || "fx" ) + "queue";
			queue = dataPriv.get( elem, type );

			// Speed up dequeue by getting out quickly if this is just a lookup
			if ( data ) {
				if ( !queue || Array.isArray( data ) ) {
					queue = dataPriv.access( elem, type, jQuery.makeArray( data ) );
				} else {
					queue.push( data );
				}
			}
			return queue || [];
		}
	},

	dequeue: function( elem, type ) {
		type = type || "fx";

		var queue = jQuery.queue( elem, type ),
			startLength = queue.length,
			fn = queue.shift(),
			hooks = jQuery._queueHooks( elem, type ),
			next = function() {
				jQuery.dequeue( elem, type );
			};

		// If the fx queue is dequeued, always remove the progress sentinel
		if ( fn === "inprogress" ) {
			fn = queue.shift();
			startLength--;
		}

		if ( fn ) {

			// Add a progress sentinel to prevent the fx queue from being
			// automatically dequeued
			if ( type === "fx" ) {
				queue.unshift( "inprogress" );
			}

			// Clear up the last queue stop function
			delete hooks.stop;
			fn.call( elem, next, hooks );
		}

		if ( !startLength && hooks ) {
			hooks.empty.fire();
		}
	},

	// Not public - generate a queueHooks object, or return the current one
	_queueHooks: function( elem, type ) {
		var key = type + "queueHooks";
		return dataPriv.get( elem, key ) || dataPriv.access( elem, key, {
			empty: jQuery.Callbacks( "once memory" ).add( function() {
				dataPriv.remove( elem, [ type + "queue", key ] );
			} )
		} );
	}
} );

jQuery.fn.extend( {
	queue: function( type, data ) {
		var setter = 2;

		if ( typeof type !== "string" ) {
			data = type;
			type = "fx";
			setter--;
		}

		if ( arguments.length < setter ) {
			return jQuery.queue( this[ 0 ], type );
		}

		return data === undefined ?
			this :
			this.each( function() {
				var queue = jQuery.queue( this, type, data );

				// Ensure a hooks for this queue
				jQuery._queueHooks( this, type );

				if ( type === "fx" && queue[ 0 ] !== "inprogress" ) {
					jQuery.dequeue( this, type );
				}
			} );
	},
	dequeue: function( type ) {
		return this.each( function() {
			jQuery.dequeue( this, type );
		} );
	},
	clearQueue: function( type ) {
		return this.queue( type || "fx", [] );
	},

	// Get a promise resolved when queues of a certain type
	// are emptied (fx is the type by default)
	promise: function( type, obj ) {
		var tmp,
			count = 1,
			defer = jQuery.Deferred(),
			elements = this,
			i = this.length,
			resolve = function() {
				if ( !( --count ) ) {
					defer.resolveWith( elements, [ elements ] );
				}
			};

		if ( typeof type !== "string" ) {
			obj = type;
			type = undefined;
		}
		type = type || "fx";

		while ( i-- ) {
			tmp = dataPriv.get( elements[ i ], type + "queueHooks" );
			if ( tmp && tmp.empty ) {
				count++;
				tmp.empty.add( resolve );
			}
		}
		resolve();
		return defer.promise( obj );
	}
} );
var pnum = ( /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/ ).source;

var rcssNum = new RegExp( "^(?:([+-])=|)(" + pnum + ")([a-z%]*)$", "i" );


var cssExpand = [ "Top", "Right", "Bottom", "Left" ];

var documentElement = document.documentElement;



	var isAttached = function( elem ) {
			return jQuery.contains( elem.ownerDocument, elem );
		},
		composed = { composed: true };

	// Support: IE 9 - 11+, Edge 12 - 18+, iOS 10.0 - 10.2 only
	// Check attachment across shadow DOM boundaries when possible (gh-3504)
	// Support: iOS 10.0-10.2 only
	// Early iOS 10 versions support `attachShadow` but not `getRootNode`,
	// leading to errors. We need to check for `getRootNode`.
	if ( documentElement.getRootNode ) {
		isAttached = function( elem ) {
			return jQuery.contains( elem.ownerDocument, elem ) ||
				elem.getRootNode( composed ) === elem.ownerDocument;
		};
	}
var isHiddenWithinTree = function( elem, el ) {

		// isHiddenWithinTree might be called from jQuery#filter function;
		// in that case, element will be second argument
		elem = el || elem;

		// Inline style trumps all
		return elem.style.display === "none" ||
			elem.style.display === "" &&

			// Otherwise, check computed style
			// Support: Firefox <=43 - 45
			// Disconnected elements can have computed display: none, so first confirm that elem is
			// in the document.
			isAttached( elem ) &&

			jQuery.css( elem, "display" ) === "none";
	};



function adjustCSS( elem, prop, valueParts, tween ) {
	var adjusted, scale,
		maxIterations = 20,
		currentValue = tween ?
			function() {
				return tween.cur();
			} :
			function() {
				return jQuery.css( elem, prop, "" );
			},
		initial = currentValue(),
		unit = valueParts && valueParts[ 3 ] || ( jQuery.cssNumber[ prop ] ? "" : "px" ),

		// Starting value computation is required for potential unit mismatches
		initialInUnit = elem.nodeType &&
			( jQuery.cssNumber[ prop ] || unit !== "px" && +initial ) &&
			rcssNum.exec( jQuery.css( elem, prop ) );

	if ( initialInUnit && initialInUnit[ 3 ] !== unit ) {

		// Support: Firefox <=54
		// Halve the iteration target value to prevent interference from CSS upper bounds (gh-2144)
		initial = initial / 2;

		// Trust units reported by jQuery.css
		unit = unit || initialInUnit[ 3 ];

		// Iteratively approximate from a nonzero starting point
		initialInUnit = +initial || 1;

		while ( maxIterations-- ) {

			// Evaluate and update our best guess (doubling guesses that zero out).
			// Finish if the scale equals or crosses 1 (making the old*new product non-positive).
			jQuery.style( elem, prop, initialInUnit + unit );
			if ( ( 1 - scale ) * ( 1 - ( scale = currentValue() / initial || 0.5 ) ) <= 0 ) {
				maxIterations = 0;
			}
			initialInUnit = initialInUnit / scale;

		}

		initialInUnit = initialInUnit * 2;
		jQuery.style( elem, prop, initialInUnit + unit );

		// Make sure we update the tween properties later on
		valueParts = valueParts || [];
	}

	if ( valueParts ) {
		initialInUnit = +initialInUnit || +initial || 0;

		// Apply relative offset (+=/-=) if specified
		adjusted = valueParts[ 1 ] ?
			initialInUnit + ( valueParts[ 1 ] + 1 ) * valueParts[ 2 ] :
			+valueParts[ 2 ];
		if ( tween ) {
			tween.unit = unit;
			tween.start = initialInUnit;
			tween.end = adjusted;
		}
	}
	return adjusted;
}


var defaultDisplayMap = {};

function getDefaultDisplay( elem ) {
	var temp,
		doc = elem.ownerDocument,
		nodeName = elem.nodeName,
		display = defaultDisplayMap[ nodeName ];

	if ( display ) {
		return display;
	}

	temp = doc.body.appendChild( doc.createElement( nodeName ) );
	display = jQuery.css( temp, "display" );

	temp.parentNode.removeChild( temp );

	if ( display === "none" ) {
		display = "block";
	}
	defaultDisplayMap[ nodeName ] = display;

	return display;
}

function showHide( elements, show ) {
	var display, elem,
		values = [],
		index = 0,
		length = elements.length;

	// Determine new display value for elements that need to change
	for ( ; index < length; index++ ) {
		elem = elements[ index ];
		if ( !elem.style ) {
			continue;
		}

		display = elem.style.display;
		if ( show ) {

			// Since we force visibility upon cascade-hidden elements, an immediate (and slow)
			// check is required in this first loop unless we have a nonempty display value (either
			// inline or about-to-be-restored)
			if ( display === "none" ) {
				values[ index ] = dataPriv.get( elem, "display" ) || null;
				if ( !values[ index ] ) {
					elem.style.display = "";
				}
			}
			if ( elem.style.display === "" && isHiddenWithinTree( elem ) ) {
				values[ index ] = getDefaultDisplay( elem );
			}
		} else {
			if ( display !== "none" ) {
				values[ index ] = "none";

				// Remember what we're overwriting
				dataPriv.set( elem, "display", display );
			}
		}
	}

	// Set the display of the elements in a second loop to avoid constant reflow
	for ( index = 0; index < length; index++ ) {
		if ( values[ index ] != null ) {
			elements[ index ].style.display = values[ index ];
		}
	}

	return elements;
}

jQuery.fn.extend( {
	show: function() {
		return showHide( this, true );
	},
	hide: function() {
		return showHide( this );
	},
	toggle: function( state ) {
		if ( typeof state === "boolean" ) {
			return state ? this.show() : this.hide();
		}

		return this.each( function() {
			if ( isHiddenWithinTree( this ) ) {
				jQuery( this ).show();
			} else {
				jQuery( this ).hide();
			}
		} );
	}
} );
var rcheckableType = ( /^(?:checkbox|radio)$/i );

var rtagName = ( /<([a-z][^\/\0>\x20\t\r\n\f]*)/i );

var rscriptType = ( /^$|^module$|\/(?:java|ecma)script/i );



( function() {
	var fragment = document.createDocumentFragment(),
		div = fragment.appendChild( document.createElement( "div" ) ),
		input = document.createElement( "input" );

	// Support: Android 4.0 - 4.3 only
	// Check state lost if the name is set (trac-11217)
	// Support: Windows Web Apps (WWA)
	// `name` and `type` must use .setAttribute for WWA (trac-14901)
	input.setAttribute( "type", "radio" );
	input.setAttribute( "checked", "checked" );
	input.setAttribute( "name", "t" );

	div.appendChild( input );

	// Support: Android <=4.1 only
	// Older WebKit doesn't clone checked state correctly in fragments
	support.checkClone = div.cloneNode( true ).cloneNode( true ).lastChild.checked;

	// Support: IE <=11 only
	// Make sure textarea (and checkbox) defaultValue is properly cloned
	div.innerHTML = "<textarea>x</textarea>";
	support.noCloneChecked = !!div.cloneNode( true ).lastChild.defaultValue;

	// Support: IE <=9 only
	// IE <=9 replaces <option> tags with their contents when inserted outside of
	// the select element.
	div.innerHTML = "<option></option>";
	support.option = !!div.lastChild;
} )();


// We have to close these tags to support XHTML (trac-13200)
var wrapMap = {

	// XHTML parsers do not magically insert elements in the
	// same way that tag soup parsers do. So we cannot shorten
	// this by omitting <tbody> or other required elements.
	thead: [ 1, "<table>", "</table>" ],
	col: [ 2, "<table><colgroup>", "</colgroup></table>" ],
	tr: [ 2, "<table><tbody>", "</tbody></table>" ],
	td: [ 3, "<table><tbody><tr>", "</tr></tbody></table>" ],

	_default: [ 0, "", "" ]
};

wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;
wrapMap.th = wrapMap.td;

// Support: IE <=9 only
if ( !support.option ) {
	wrapMap.optgroup = wrapMap.option = [ 1, "<select multiple='multiple'>", "</select>" ];
}


function getAll( context, tag ) {

	// Support: IE <=9 - 11 only
	// Use typeof to avoid zero-argument method invocation on host objects (trac-15151)
	var ret;

	if ( typeof context.getElementsByTagName !== "undefined" ) {
		ret = context.getElementsByTagName( tag || "*" );

	} else if ( typeof context.querySelectorAll !== "undefined" ) {
		ret = context.querySelectorAll( tag || "*" );

	} else {
		ret = [];
	}

	if ( tag === undefined || tag && nodeName( context, tag ) ) {
		return jQuery.merge( [ context ], ret );
	}

	return ret;
}


// Mark scripts as having already been evaluated
function setGlobalEval( elems, refElements ) {
	var i = 0,
		l = elems.length;

	for ( ; i < l; i++ ) {
		dataPriv.set(
			elems[ i ],
			"globalEval",
			!refElements || dataPriv.get( refElements[ i ], "globalEval" )
		);
	}
}


var rhtml = /<|&#?\w+;/;

function buildFragment( elems, context, scripts, selection, ignored ) {
	var elem, tmp, tag, wrap, attached, j,
		fragment = context.createDocumentFragment(),
		nodes = [],
		i = 0,
		l = elems.length;

	for ( ; i < l; i++ ) {
		elem = elems[ i ];

		if ( elem || elem === 0 ) {

			// Add nodes directly
			if ( toType( elem ) === "object" ) {

				// Support: Android <=4.0 only, PhantomJS 1 only
				// push.apply(_, arraylike) throws on ancient WebKit
				jQuery.merge( nodes, elem.nodeType ? [ elem ] : elem );

			// Convert non-html into a text node
			} else if ( !rhtml.test( elem ) ) {
				nodes.push( context.createTextNode( elem ) );

			// Convert html into DOM nodes
			} else {
				tmp = tmp || fragment.appendChild( context.createElement( "div" ) );

				// Deserialize a standard representation
				tag = ( rtagName.exec( elem ) || [ "", "" ] )[ 1 ].toLowerCase();
				wrap = wrapMap[ tag ] || wrapMap._default;
				tmp.innerHTML = wrap[ 1 ] + jQuery.htmlPrefilter( elem ) + wrap[ 2 ];

				// Descend through wrappers to the right content
				j = wrap[ 0 ];
				while ( j-- ) {
					tmp = tmp.lastChild;
				}

				// Support: Android <=4.0 only, PhantomJS 1 only
				// push.apply(_, arraylike) throws on ancient WebKit
				jQuery.merge( nodes, tmp.childNodes );

				// Remember the top-level container
				tmp = fragment.firstChild;

				// Ensure the created nodes are orphaned (trac-12392)
				tmp.textContent = "";
			}
		}
	}

	// Remove wrapper from fragment
	fragment.textContent = "";

	i = 0;
	while ( ( elem = nodes[ i++ ] ) ) {

		// Skip elements already in the context collection (trac-4087)
		if ( selection && jQuery.inArray( elem, selection ) > -1 ) {
			if ( ignored ) {
				ignored.push( elem );
			}
			continue;
		}

		attached = isAttached( elem );

		// Append to fragment
		tmp = getAll( fragment.appendChild( elem ), "script" );

		// Preserve script evaluation history
		if ( attached ) {
			setGlobalEval( tmp );
		}

		// Capture executables
		if ( scripts ) {
			j = 0;
			while ( ( elem = tmp[ j++ ] ) ) {
				if ( rscriptType.test( elem.type || "" ) ) {
					scripts.push( elem );
				}
			}
		}
	}

	return fragment;
}


var rtypenamespace = /^([^.]*)(?:\.(.+)|)/;

function returnTrue() {
	return true;
}

function returnFalse() {
	return false;
}

// Support: IE <=9 - 11+
// focus() and blur() are asynchronous, except when they are no-op.
// So expect focus to be synchronous when the element is already active,
// and blur to be synchronous when the element is not already active.
// (focus and blur are always synchronous in other supported browsers,
// this just defines when we can count on it).
function expectSync( elem, type ) {
	return ( elem === safeActiveElement() ) === ( type === "focus" );
}

// Support: IE <=9 only
// Accessing document.activeElement can throw unexpectedly
// https://bugs.jquery.com/ticket/13393
function safeActiveElement() {
	try {
		return document.activeElement;
	} catch ( err ) { }
}

function on( elem, types, selector, data, fn, one ) {
	var origFn, type;

	// Types can be a map of types/handlers
	if ( typeof types === "object" ) {

		// ( types-Object, selector, data )
		if ( typeof selector !== "string" ) {

			// ( types-Object, data )
			data = data || selector;
			selector = undefined;
		}
		for ( type in types ) {
			on( elem, type, selector, data, types[ type ], one );
		}
		return elem;
	}

	if ( data == null && fn == null ) {

		// ( types, fn )
		fn = selector;
		data = selector = undefined;
	} else if ( fn == null ) {
		if ( typeof selector === "string" ) {

			// ( types, selector, fn )
			fn = data;
			data = undefined;
		} else {

			// ( types, data, fn )
			fn = data;
			data = selector;
			selector = undefined;
		}
	}
	if ( fn === false ) {
		fn = returnFalse;
	} else if ( !fn ) {
		return elem;
	}

	if ( one === 1 ) {
		origFn = fn;
		fn = function( event ) {

			// Can use an empty set, since event contains the info
			jQuery().off( event );
			return origFn.apply( this, arguments );
		};

		// Use same guid so caller can remove using origFn
		fn.guid = origFn.guid || ( origFn.guid = jQuery.guid++ );
	}
	return elem.each( function() {
		jQuery.event.add( this, types, fn, data, selector );
	} );
}

/*
 * Helper functions for managing events -- not part of the public interface.
 * Props to Dean Edwards' addEvent library for many of the ideas.
 */
jQuery.event = {

	global: {},

	add: function( elem, types, handler, data, selector ) {

		var handleObjIn, eventHandle, tmp,
			events, t, handleObj,
			special, handlers, type, namespaces, origType,
			elemData = dataPriv.get( elem );

		// Only attach events to objects that accept data
		if ( !acceptData( elem ) ) {
			return;
		}

		// Caller can pass in an object of custom data in lieu of the handler
		if ( handler.handler ) {
			handleObjIn = handler;
			handler = handleObjIn.handler;
			selector = handleObjIn.selector;
		}

		// Ensure that invalid selectors throw exceptions at attach time
		// Evaluate against documentElement in case elem is a non-element node (e.g., document)
		if ( selector ) {
			jQuery.find.matchesSelector( documentElement, selector );
		}

		// Make sure that the handler has a unique ID, used to find/remove it later
		if ( !handler.guid ) {
			handler.guid = jQuery.guid++;
		}

		// Init the element's event structure and main handler, if this is the first
		if ( !( events = elemData.events ) ) {
			events = elemData.events = Object.create( null );
		}
		if ( !( eventHandle = elemData.handle ) ) {
			eventHandle = elemData.handle = function( e ) {

				// Discard the second event of a jQuery.event.trigger() and
				// when an event is called after a page has unloaded
				return typeof jQuery !== "undefined" && jQuery.event.triggered !== e.type ?
					jQuery.event.dispatch.apply( elem, arguments ) : undefined;
			};
		}

		// Handle multiple events separated by a space
		types = ( types || "" ).match( rnothtmlwhite ) || [ "" ];
		t = types.length;
		while ( t-- ) {
			tmp = rtypenamespace.exec( types[ t ] ) || [];
			type = origType = tmp[ 1 ];
			namespaces = ( tmp[ 2 ] || "" ).split( "." ).sort();

			// There *must* be a type, no attaching namespace-only handlers
			if ( !type ) {
				continue;
			}

			// If event changes its type, use the special event handlers for the changed type
			special = jQuery.event.special[ type ] || {};

			// If selector defined, determine special event api type, otherwise given type
			type = ( selector ? special.delegateType : special.bindType ) || type;

			// Update special based on newly reset type
			special = jQuery.event.special[ type ] || {};

			// handleObj is passed to all event handlers
			handleObj = jQuery.extend( {
				type: type,
				origType: origType,
				data: data,
				handler: handler,
				guid: handler.guid,
				selector: selector,
				needsContext: selector && jQuery.expr.match.needsContext.test( selector ),
				namespace: namespaces.join( "." )
			}, handleObjIn );

			// Init the event handler queue if we're the first
			if ( !( handlers = events[ type ] ) ) {
				handlers = events[ type ] = [];
				handlers.delegateCount = 0;

				// Only use addEventListener if the special events handler returns false
				if ( !special.setup ||
					special.setup.call( elem, data, namespaces, eventHandle ) === false ) {

					if ( elem.addEventListener ) {
						elem.addEventListener( type, eventHandle );
					}
				}
			}

			if ( special.add ) {
				special.add.call( elem, handleObj );

				if ( !handleObj.handler.guid ) {
					handleObj.handler.guid = handler.guid;
				}
			}

			// Add to the element's handler list, delegates in front
			if ( selector ) {
				handlers.splice( handlers.delegateCount++, 0, handleObj );
			} else {
				handlers.push( handleObj );
			}

			// Keep track of which events have ever been used, for event optimization
			jQuery.event.global[ type ] = true;
		}

	},

	// Detach an event or set of events from an element
	remove: function( elem, types, handler, selector, mappedTypes ) {

		var j, origCount, tmp,
			events, t, handleObj,
			special, handlers, type, namespaces, origType,
			elemData = dataPriv.hasData( elem ) && dataPriv.get( elem );

		if ( !elemData || !( events = elemData.events ) ) {
			return;
		}

		// Once for each type.namespace in types; type may be omitted
		types = ( types || "" ).match( rnothtmlwhite ) || [ "" ];
		t = types.length;
		while ( t-- ) {
			tmp = rtypenamespace.exec( types[ t ] ) || [];
			type = origType = tmp[ 1 ];
			namespaces = ( tmp[ 2 ] || "" ).split( "." ).sort();

			// Unbind all events (on this namespace, if provided) for the element
			if ( !type ) {
				for ( type in events ) {
					jQuery.event.remove( elem, type + types[ t ], handler, selector, true );
				}
				continue;
			}

			special = jQuery.event.special[ type ] || {};
			type = ( selector ? special.delegateType : special.bindType ) || type;
			handlers = events[ type ] || [];
			tmp = tmp[ 2 ] &&
				new RegExp( "(^|\\.)" + namespaces.join( "\\.(?:.*\\.|)" ) + "(\\.|$)" );

			// Remove matching events
			origCount = j = handlers.length;
			while ( j-- ) {
				handleObj = handlers[ j ];

				if ( ( mappedTypes || origType === handleObj.origType ) &&
					( !handler || handler.guid === handleObj.guid ) &&
					( !tmp || tmp.test( handleObj.namespace ) ) &&
					( !selector || selector === handleObj.selector ||
						selector === "**" && handleObj.selector ) ) {
					handlers.splice( j, 1 );

					if ( handleObj.selector ) {
						handlers.delegateCount--;
					}
					if ( special.remove ) {
						special.remove.call( elem, handleObj );
					}
				}
			}

			// Remove generic event handler if we removed something and no more handlers exist
			// (avoids potential for endless recursion during removal of special event handlers)
			if ( origCount && !handlers.length ) {
				if ( !special.teardown ||
					special.teardown.call( elem, namespaces, elemData.handle ) === false ) {

					jQuery.removeEvent( elem, type, elemData.handle );
				}

				delete events[ type ];
			}
		}

		// Remove data and the expando if it's no longer used
		if ( jQuery.isEmptyObject( events ) ) {
			dataPriv.remove( elem, "handle events" );
		}
	},

	dispatch: function( nativeEvent ) {

		var i, j, ret, matched, handleObj, handlerQueue,
			args = new Array( arguments.length ),

			// Make a writable jQuery.Event from the native event object
			event = jQuery.event.fix( nativeEvent ),

			handlers = (
				dataPriv.get( this, "events" ) || Object.create( null )
			)[ event.type ] || [],
			special = jQuery.event.special[ event.type ] || {};

		// Use the fix-ed jQuery.Event rather than the (read-only) native event
		args[ 0 ] = event;

		for ( i = 1; i < arguments.length; i++ ) {
			args[ i ] = arguments[ i ];
		}

		event.delegateTarget = this;

		// Call the preDispatch hook for the mapped type, and let it bail if desired
		if ( special.preDispatch && special.preDispatch.call( this, event ) === false ) {
			return;
		}

		// Determine handlers
		handlerQueue = jQuery.event.handlers.call( this, event, handlers );

		// Run delegates first; they may want to stop propagation beneath us
		i = 0;
		while ( ( matched = handlerQueue[ i++ ] ) && !event.isPropagationStopped() ) {
			event.currentTarget = matched.elem;

			j = 0;
			while ( ( handleObj = matched.handlers[ j++ ] ) &&
				!event.isImmediatePropagationStopped() ) {

				// If the event is namespaced, then each handler is only invoked if it is
				// specially universal or its namespaces are a superset of the event's.
				if ( !event.rnamespace || handleObj.namespace === false ||
					event.rnamespace.test( handleObj.namespace ) ) {

					event.handleObj = handleObj;
					event.data = handleObj.data;

					ret = ( ( jQuery.event.special[ handleObj.origType ] || {} ).handle ||
						handleObj.handler ).apply( matched.elem, args );

					if ( ret !== undefined ) {
						if ( ( event.result = ret ) === false ) {
							event.preventDefault();
							event.stopPropagation();
						}
					}
				}
			}
		}

		// Call the postDispatch hook for the mapped type
		if ( special.postDispatch ) {
			special.postDispatch.call( this, event );
		}

		return event.result;
	},

	handlers: function( event, handlers ) {
		var i, handleObj, sel, matchedHandlers, matchedSelectors,
			handlerQueue = [],
			delegateCount = handlers.delegateCount,
			cur = event.target;

		// Find delegate handlers
		if ( delegateCount &&

			// Support: IE <=9
			// Black-hole SVG <use> instance trees (trac-13180)
			cur.nodeType &&

			// Support: Firefox <=42
			// Suppress spec-violating clicks indicating a non-primary pointer button (trac-3861)
			// https://www.w3.org/TR/DOM-Level-3-Events/#event-type-click
			// Support: IE 11 only
			// ...but not arrow key "clicks" of radio inputs, which can have `button` -1 (gh-2343)
			!( event.type === "click" && event.button >= 1 ) ) {

			for ( ; cur !== this; cur = cur.parentNode || this ) {

				// Don't check non-elements (trac-13208)
				// Don't process clicks on disabled elements (trac-6911, trac-8165, trac-11382, trac-11764)
				if ( cur.nodeType === 1 && !( event.type === "click" && cur.disabled === true ) ) {
					matchedHandlers = [];
					matchedSelectors = {};
					for ( i = 0; i < delegateCount; i++ ) {
						handleObj = handlers[ i ];

						// Don't conflict with Object.prototype properties (trac-13203)
						sel = handleObj.selector + " ";

						if ( matchedSelectors[ sel ] === undefined ) {
							matchedSelectors[ sel ] = handleObj.needsContext ?
								jQuery( sel, this ).index( cur ) > -1 :
								jQuery.find( sel, this, null, [ cur ] ).length;
						}
						if ( matchedSelectors[ sel ] ) {
							matchedHandlers.push( handleObj );
						}
					}
					if ( matchedHandlers.length ) {
						handlerQueue.push( { elem: cur, handlers: matchedHandlers } );
					}
				}
			}
		}

		// Add the remaining (directly-bound) handlers
		cur = this;
		if ( delegateCount < handlers.length ) {
			handlerQueue.push( { elem: cur, handlers: handlers.slice( delegateCount ) } );
		}

		return handlerQueue;
	},

	addProp: function( name, hook ) {
		Object.defineProperty( jQuery.Event.prototype, name, {
			enumerable: true,
			configurable: true,

			get: isFunction( hook ) ?
				function() {
					if ( this.originalEvent ) {
						return hook( this.originalEvent );
					}
				} :
				function() {
					if ( this.originalEvent ) {
						return this.originalEvent[ name ];
					}
				},

			set: function( value ) {
				Object.defineProperty( this, name, {
					enumerable: true,
					configurable: true,
					writable: true,
					value: value
				} );
			}
		} );
	},

	fix: function( originalEvent ) {
		return originalEvent[ jQuery.expando ] ?
			originalEvent :
			new jQuery.Event( originalEvent );
	},

	special: {
		load: {

			// Prevent triggered image.load events from bubbling to window.load
			noBubble: true
		},
		click: {

			// Utilize native event to ensure correct state for checkable inputs
			setup: function( data ) {

				// For mutual compressibility with _default, replace `this` access with a local var.
				// `|| data` is dead code meant only to preserve the variable through minification.
				var el = this || data;

				// Claim the first handler
				if ( rcheckableType.test( el.type ) &&
					el.click && nodeName( el, "input" ) ) {

					// dataPriv.set( el, "click", ... )
					leverageNative( el, "click", returnTrue );
				}

				// Return false to allow normal processing in the caller
				return false;
			},
			trigger: function( data ) {

				// For mutual compressibility with _default, replace `this` access with a local var.
				// `|| data` is dead code meant only to preserve the variable through minification.
				var el = this || data;

				// Force setup before triggering a click
				if ( rcheckableType.test( el.type ) &&
					el.click && nodeName( el, "input" ) ) {

					leverageNative( el, "click" );
				}

				// Return non-false to allow normal event-path propagation
				return true;
			},

			// For cross-browser consistency, suppress native .click() on links
			// Also prevent it if we're currently inside a leveraged native-event stack
			_default: function( event ) {
				var target = event.target;
				return rcheckableType.test( target.type ) &&
					target.click && nodeName( target, "input" ) &&
					dataPriv.get( target, "click" ) ||
					nodeName( target, "a" );
			}
		},

		beforeunload: {
			postDispatch: function( event ) {

				// Support: Firefox 20+
				// Firefox doesn't alert if the returnValue field is not set.
				if ( event.result !== undefined && event.originalEvent ) {
					event.originalEvent.returnValue = event.result;
				}
			}
		}
	}
};

// Ensure the presence of an event listener that handles manually-triggered
// synthetic events by interrupting progress until reinvoked in response to
// *native* events that it fires directly, ensuring that state changes have
// already occurred before other listeners are invoked.
function leverageNative( el, type, expectSync ) {

	// Missing expectSync indicates a trigger call, which must force setup through jQuery.event.add
	if ( !expectSync ) {
		if ( dataPriv.get( el, type ) === undefined ) {
			jQuery.event.add( el, type, returnTrue );
		}
		return;
	}

	// Register the controller as a special universal handler for all event namespaces
	dataPriv.set( el, type, false );
	jQuery.event.add( el, type, {
		namespace: false,
		handler: function( event ) {
			var notAsync, result,
				saved = dataPriv.get( this, type );

			if ( ( event.isTrigger & 1 ) && this[ type ] ) {

				// Interrupt processing of the outer synthetic .trigger()ed event
				// Saved data should be false in such cases, but might be a leftover capture object
				// from an async native handler (gh-4350)
				if ( !saved.length ) {

					// Store arguments for use when handling the inner native event
					// There will always be at least one argument (an event object), so this array
					// will not be confused with a leftover capture object.
					saved = slice.call( arguments );
					dataPriv.set( this, type, saved );

					// Trigger the native event and capture its result
					// Support: IE <=9 - 11+
					// focus() and blur() are asynchronous
					notAsync = expectSync( this, type );
					this[ type ]();
					result = dataPriv.get( this, type );
					if ( saved !== result || notAsync ) {
						dataPriv.set( this, type, false );
					} else {
						result = {};
					}
					if ( saved !== result ) {

						// Cancel the outer synthetic event
						event.stopImmediatePropagation();
						event.preventDefault();

						// Support: Chrome 86+
						// In Chrome, if an element having a focusout handler is blurred by
						// clicking outside of it, it invokes the handler synchronously. If
						// that handler calls `.remove()` on the element, the data is cleared,
						// leaving `result` undefined. We need to guard against this.
						return result && result.value;
					}

				// If this is an inner synthetic event for an event with a bubbling surrogate
				// (focus or blur), assume that the surrogate already propagated from triggering the
				// native event and prevent that from happening again here.
				// This technically gets the ordering wrong w.r.t. to `.trigger()` (in which the
				// bubbling surrogate propagates *after* the non-bubbling base), but that seems
				// less bad than duplication.
				} else if ( ( jQuery.event.special[ type ] || {} ).delegateType ) {
					event.stopPropagation();
				}

			// If this is a native event triggered above, everything is now in order
			// Fire an inner synthetic event with the original arguments
			} else if ( saved.length ) {

				// ...and capture the result
				dataPriv.set( this, type, {
					value: jQuery.event.trigger(

						// Support: IE <=9 - 11+
						// Extend with the prototype to reset the above stopImmediatePropagation()
						jQuery.extend( saved[ 0 ], jQuery.Event.prototype ),
						saved.slice( 1 ),
						this
					)
				} );

				// Abort handling of the native event
				event.stopImmediatePropagation();
			}
		}
	} );
}

jQuery.removeEvent = function( elem, type, handle ) {

	// This "if" is needed for plain objects
	if ( elem.removeEventListener ) {
		elem.removeEventListener( type, handle );
	}
};

jQuery.Event = function( src, props ) {

	// Allow instantiation without the 'new' keyword
	if ( !( this instanceof jQuery.Event ) ) {
		return new jQuery.Event( src, props );
	}

	// Event object
	if ( src && src.type ) {
		this.originalEvent = src;
		this.type = src.type;

		// Events bubbling up the document may have been marked as prevented
		// by a handler lower down the tree; reflect the correct value.
		this.isDefaultPrevented = src.defaultPrevented ||
				src.defaultPrevented === undefined &&

				// Support: Android <=2.3 only
				src.returnValue === false ?
			returnTrue :
			returnFalse;

		// Create target properties
		// Support: Safari <=6 - 7 only
		// Target should not be a text node (trac-504, trac-13143)
		this.target = ( src.target && src.target.nodeType === 3 ) ?
			src.target.parentNode :
			src.target;

		this.currentTarget = src.currentTarget;
		this.relatedTarget = src.relatedTarget;

	// Event type
	} else {
		this.type = src;
	}

	// Put explicitly provided properties onto the event object
	if ( props ) {
		jQuery.extend( this, props );
	}

	// Create a timestamp if incoming event doesn't have one
	this.timeStamp = src && src.timeStamp || Date.now();

	// Mark it as fixed
	this[ jQuery.expando ] = true;
};

// jQuery.Event is based on DOM3 Events as specified by the ECMAScript Language Binding
// https://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
jQuery.Event.prototype = {
	constructor: jQuery.Event,
	isDefaultPrevented: returnFalse,
	isPropagationStopped: returnFalse,
	isImmediatePropagationStopped: returnFalse,
	isSimulated: false,

	preventDefault: function() {
		var e = this.originalEvent;

		this.isDefaultPrevented = returnTrue;

		if ( e && !this.isSimulated ) {
			e.preventDefault();
		}
	},
	stopPropagation: function() {
		var e = this.originalEvent;

		this.isPropagationStopped = returnTrue;

		if ( e && !this.isSimulated ) {
			e.stopPropagation();
		}
	},
	stopImmediatePropagation: function() {
		var e = this.originalEvent;

		this.isImmediatePropagationStopped = returnTrue;

		if ( e && !this.isSimulated ) {
			e.stopImmediatePropagation();
		}

		this.stopPropagation();
	}
};

// Includes all common event props including KeyEvent and MouseEvent specific props
jQuery.each( {
	altKey: true,
	bubbles: true,
	cancelable: true,
	changedTouches: true,
	ctrlKey: true,
	detail: true,
	eventPhase: true,
	metaKey: true,
	pageX: true,
	pageY: true,
	shiftKey: true,
	view: true,
	"char": true,
	code: true,
	charCode: true,
	key: true,
	keyCode: true,
	button: true,
	buttons: true,
	clientX: true,
	clientY: true,
	offsetX: true,
	offsetY: true,
	pointerId: true,
	pointerType: true,
	screenX: true,
	screenY: true,
	targetTouches: true,
	toElement: true,
	touches: true,
	which: true
}, jQuery.event.addProp );

jQuery.each( { focus: "focusin", blur: "focusout" }, function( type, delegateType ) {
	jQuery.event.special[ type ] = {

		// Utilize native event if possible so blur/focus sequence is correct
		setup: function() {

			// Claim the first handler
			// dataPriv.set( this, "focus", ... )
			// dataPriv.set( this, "blur", ... )
			leverageNative( this, type, expectSync );

			// Return false to allow normal processing in the caller
			return false;
		},
		trigger: function() {

			// Force setup before trigger
			leverageNative( this, type );

			// Return non-false to allow normal event-path propagation
			return true;
		},

		// Suppress native focus or blur if we're currently inside
		// a leveraged native-event stack
		_default: function( event ) {
			return dataPriv.get( event.target, type );
		},

		delegateType: delegateType
	};
} );

// Create mouseenter/leave events using mouseover/out and event-time checks
// so that event delegation works in jQuery.
// Do the same for pointerenter/pointerleave and pointerover/pointerout
//
// Support: Safari 7 only
// Safari sends mouseenter too often; see:
// https://bugs.chromium.org/p/chromium/issues/detail?id=470258
// for the description of the bug (it existed in older Chrome versions as well).
jQuery.each( {
	mouseenter: "mouseover",
	mouseleave: "mouseout",
	pointerenter: "pointerover",
	pointerleave: "pointerout"
}, function( orig, fix ) {
	jQuery.event.special[ orig ] = {
		delegateType: fix,
		bindType: fix,

		handle: function( event ) {
			var ret,
				target = this,
				related = event.relatedTarget,
				handleObj = event.handleObj;

			// For mouseenter/leave call the handler if related is outside the target.
			// NB: No relatedTarget if the mouse left/entered the browser window
			if ( !related || ( related !== target && !jQuery.contains( target, related ) ) ) {
				event.type = handleObj.origType;
				ret = handleObj.handler.apply( this, arguments );
				event.type = fix;
			}
			return ret;
		}
	};
} );

jQuery.fn.extend( {

	on: function( types, selector, data, fn ) {
		return on( this, types, selector, data, fn );
	},
	one: function( types, selector, data, fn ) {
		return on( this, types, selector, data, fn, 1 );
	},
	off: function( types, selector, fn ) {
		var handleObj, type;
		if ( types && types.preventDefault && types.handleObj ) {

			// ( event )  dispatched jQuery.Event
			handleObj = types.handleObj;
			jQuery( types.delegateTarget ).off(
				handleObj.namespace ?
					handleObj.origType + "." + handleObj.namespace :
					handleObj.origType,
				handleObj.selector,
				handleObj.handler
			);
			return this;
		}
		if ( typeof types === "object" ) {

			// ( types-object [, selector] )
			for ( type in types ) {
				this.off( type, selector, types[ type ] );
			}
			return this;
		}
		if ( selector === false || typeof selector === "function" ) {

			// ( types [, fn] )
			fn = selector;
			selector = undefined;
		}
		if ( fn === false ) {
			fn = returnFalse;
		}
		return this.each( function() {
			jQuery.event.remove( this, types, fn, selector );
		} );
	}
} );


var

	// Support: IE <=10 - 11, Edge 12 - 13 only
	// In IE/Edge using regex groups here causes severe slowdowns.
	// See https://connect.microsoft.com/IE/feedback/details/1736512/
	rnoInnerhtml = /<script|<style|<link/i,

	// checked="checked" or checked
	rchecked = /checked\s*(?:[^=]|=\s*.checked.)/i,

	rcleanScript = /^\s*<!\[CDATA\[|\]\]>\s*$/g;

// Prefer a tbody over its parent table for containing new rows
function manipulationTarget( elem, content ) {
	if ( nodeName( elem, "table" ) &&
		nodeName( content.nodeType !== 11 ? content : content.firstChild, "tr" ) ) {

		return jQuery( elem ).children( "tbody" )[ 0 ] || elem;
	}

	return elem;
}

// Replace/restore the type attribute of script elements for safe DOM manipulation
function disableScript( elem ) {
	elem.type = ( elem.getAttribute( "type" ) !== null ) + "/" + elem.type;
	return elem;
}
function restoreScript( elem ) {
	if ( ( elem.type || "" ).slice( 0, 5 ) === "true/" ) {
		elem.type = elem.type.slice( 5 );
	} else {
		elem.removeAttribute( "type" );
	}

	return elem;
}

function cloneCopyEvent( src, dest ) {
	var i, l, type, pdataOld, udataOld, udataCur, events;

	if ( dest.nodeType !== 1 ) {
		return;
	}

	// 1. Copy private data: events, handlers, etc.
	if ( dataPriv.hasData( src ) ) {
		pdataOld = dataPriv.get( src );
		events = pdataOld.events;

		if ( events ) {
			dataPriv.remove( dest, "handle events" );

			for ( type in events ) {
				for ( i = 0, l = events[ type ].length; i < l; i++ ) {
					jQuery.event.add( dest, type, events[ type ][ i ] );
				}
			}
		}
	}

	// 2. Copy user data
	if ( dataUser.hasData( src ) ) {
		udataOld = dataUser.access( src );
		udataCur = jQuery.extend( {}, udataOld );

		dataUser.set( dest, udataCur );
	}
}

// Fix IE bugs, see support tests
function fixInput( src, dest ) {
	var nodeName = dest.nodeName.toLowerCase();

	// Fails to persist the checked state of a cloned checkbox or radio button.
	if ( nodeName === "input" && rcheckableType.test( src.type ) ) {
		dest.checked = src.checked;

	// Fails to return the selected option to the default selected state when cloning options
	} else if ( nodeName === "input" || nodeName === "textarea" ) {
		dest.defaultValue = src.defaultValue;
	}
}

function domManip( collection, args, callback, ignored ) {

	// Flatten any nested arrays
	args = flat( args );

	var fragment, first, scripts, hasScripts, node, doc,
		i = 0,
		l = collection.length,
		iNoClone = l - 1,
		value = args[ 0 ],
		valueIsFunction = isFunction( value );

	// We can't cloneNode fragments that contain checked, in WebKit
	if ( valueIsFunction ||
			( l > 1 && typeof value === "string" &&
				!support.checkClone && rchecked.test( value ) ) ) {
		return collection.each( function( index ) {
			var self = collection.eq( index );
			if ( valueIsFunction ) {
				args[ 0 ] = value.call( this, index, self.html() );
			}
			domManip( self, args, callback, ignored );
		} );
	}

	if ( l ) {
		fragment = buildFragment( args, collection[ 0 ].ownerDocument, false, collection, ignored );
		first = fragment.firstChild;

		if ( fragment.childNodes.length === 1 ) {
			fragment = first;
		}

		// Require either new content or an interest in ignored elements to invoke the callback
		if ( first || ignored ) {
			scripts = jQuery.map( getAll( fragment, "script" ), disableScript );
			hasScripts = scripts.length;

			// Use the original fragment for the last item
			// instead of the first because it can end up
			// being emptied incorrectly in certain situations (trac-8070).
			for ( ; i < l; i++ ) {
				node = fragment;

				if ( i !== iNoClone ) {
					node = jQuery.clone( node, true, true );

					// Keep references to cloned scripts for later restoration
					if ( hasScripts ) {

						// Support: Android <=4.0 only, PhantomJS 1 only
						// push.apply(_, arraylike) throws on ancient WebKit
						jQuery.merge( scripts, getAll( node, "script" ) );
					}
				}

				callback.call( collection[ i ], node, i );
			}

			if ( hasScripts ) {
				doc = scripts[ scripts.length - 1 ].ownerDocument;

				// Reenable scripts
				jQuery.map( scripts, restoreScript );

				// Evaluate executable scripts on first document insertion
				for ( i = 0; i < hasScripts; i++ ) {
					node = scripts[ i ];
					if ( rscriptType.test( node.type || "" ) &&
						!dataPriv.access( node, "globalEval" ) &&
						jQuery.contains( doc, node ) ) {

						if ( node.src && ( node.type || "" ).toLowerCase()  !== "module" ) {

							// Optional AJAX dependency, but won't run scripts if not present
							if ( jQuery._evalUrl && !node.noModule ) {
								jQuery._evalUrl( node.src, {
									nonce: node.nonce || node.getAttribute( "nonce" )
								}, doc );
							}
						} else {

							// Unwrap a CDATA section containing script contents. This shouldn't be
							// needed as in XML documents they're already not visible when
							// inspecting element contents and in HTML documents they have no
							// meaning but we're preserving that logic for backwards compatibility.
							// This will be removed completely in 4.0. See gh-4904.
							DOMEval( node.textContent.replace( rcleanScript, "" ), node, doc );
						}
					}
				}
			}
		}
	}

	return collection;
}

function remove( elem, selector, keepData ) {
	var node,
		nodes = selector ? jQuery.filter( selector, elem ) : elem,
		i = 0;

	for ( ; ( node = nodes[ i ] ) != null; i++ ) {
		if ( !keepData && node.nodeType === 1 ) {
			jQuery.cleanData( getAll( node ) );
		}

		if ( node.parentNode ) {
			if ( keepData && isAttached( node ) ) {
				setGlobalEval( getAll( node, "script" ) );
			}
			node.parentNode.removeChild( node );
		}
	}

	return elem;
}

jQuery.extend( {
	htmlPrefilter: function( html ) {
		return html;
	},

	clone: function( elem, dataAndEvents, deepDataAndEvents ) {
		var i, l, srcElements, destElements,
			clone = elem.cloneNode( true ),
			inPage = isAttached( elem );

		// Fix IE cloning issues
		if ( !support.noCloneChecked && ( elem.nodeType === 1 || elem.nodeType === 11 ) &&
				!jQuery.isXMLDoc( elem ) ) {

			// We eschew Sizzle here for performance reasons: https://jsperf.com/getall-vs-sizzle/2
			destElements = getAll( clone );
			srcElements = getAll( elem );

			for ( i = 0, l = srcElements.length; i < l; i++ ) {
				fixInput( srcElements[ i ], destElements[ i ] );
			}
		}

		// Copy the events from the original to the clone
		if ( dataAndEvents ) {
			if ( deepDataAndEvents ) {
				srcElements = srcElements || getAll( elem );
				destElements = destElements || getAll( clone );

				for ( i = 0, l = srcElements.length; i < l; i++ ) {
					cloneCopyEvent( srcElements[ i ], destElements[ i ] );
				}
			} else {
				cloneCopyEvent( elem, clone );
			}
		}

		// Preserve script evaluation history
		destElements = getAll( clone, "script" );
		if ( destElements.length > 0 ) {
			setGlobalEval( destElements, !inPage && getAll( elem, "script" ) );
		}

		// Return the cloned set
		return clone;
	},

	cleanData: function( elems ) {
		var data, elem, type,
			special = jQuery.event.special,
			i = 0;

		for ( ; ( elem = elems[ i ] ) !== undefined; i++ ) {
			if ( acceptData( elem ) ) {
				if ( ( data = elem[ dataPriv.expando ] ) ) {
					if ( data.events ) {
						for ( type in data.events ) {
							if ( special[ type ] ) {
								jQuery.event.remove( elem, type );

							// This is a shortcut to avoid jQuery.event.remove's overhead
							} else {
								jQuery.removeEvent( elem, type, data.handle );
							}
						}
					}

					// Support: Chrome <=35 - 45+
					// Assign undefined instead of using delete, see Data#remove
					elem[ dataPriv.expando ] = undefined;
				}
				if ( elem[ dataUser.expando ] ) {

					// Support: Chrome <=35 - 45+
					// Assign undefined instead of using delete, see Data#remove
					elem[ dataUser.expando ] = undefined;
				}
			}
		}
	}
} );

jQuery.fn.extend( {
	detach: function( selector ) {
		return remove( this, selector, true );
	},

	remove: function( selector ) {
		return remove( this, selector );
	},

	text: function( value ) {
		return access( this, function( value ) {
			return value === undefined ?
				jQuery.text( this ) :
				this.empty().each( function() {
					if ( this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9 ) {
						this.textContent = value;
					}
				} );
		}, null, value, arguments.length );
	},

	append: function() {
		return domManip( this, arguments, function( elem ) {
			if ( this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9 ) {
				var target = manipulationTarget( this, elem );
				target.appendChild( elem );
			}
		} );
	},

	prepend: function() {
		return domManip( this, arguments, function( elem ) {
			if ( this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9 ) {
				var target = manipulationTarget( this, elem );
				target.insertBefore( elem, target.firstChild );
			}
		} );
	},

	before: function() {
		return domManip( this, arguments, function( elem ) {
			if ( this.parentNode ) {
				this.parentNode.insertBefore( elem, this );
			}
		} );
	},

	after: function() {
		return domManip( this, arguments, function( elem ) {
			if ( this.parentNode ) {
				this.parentNode.insertBefore( elem, this.nextSibling );
			}
		} );
	},

	empty: function() {
		var elem,
			i = 0;

		for ( ; ( elem = this[ i ] ) != null; i++ ) {
			if ( elem.nodeType === 1 ) {

				// Prevent memory leaks
				jQuery.cleanData( getAll( elem, false ) );

				// Remove any remaining nodes
				elem.textContent = "";
			}
		}

		return this;
	},

	clone: function( dataAndEvents, deepDataAndEvents ) {
		dataAndEvents = dataAndEvents == null ? false : dataAndEvents;
		deepDataAndEvents = deepDataAndEvents == null ? dataAndEvents : deepDataAndEvents;

		return this.map( function() {
			return jQuery.clone( this, dataAndEvents, deepDataAndEvents );
		} );
	},

	html: function( value ) {
		return access( this, function( value ) {
			var elem = this[ 0 ] || {},
				i = 0,
				l = this.length;

			if ( value === undefined && elem.nodeType === 1 ) {
				return elem.innerHTML;
			}

			// See if we can take a shortcut and just use innerHTML
			if ( typeof value === "string" && !rnoInnerhtml.test( value ) &&
				!wrapMap[ ( rtagName.exec( value ) || [ "", "" ] )[ 1 ].toLowerCase() ] ) {

				value = jQuery.htmlPrefilter( value );

				try {
					for ( ; i < l; i++ ) {
						elem = this[ i ] || {};

						// Remove element nodes and prevent memory leaks
						if ( elem.nodeType === 1 ) {
							jQuery.cleanData( getAll( elem, false ) );
							elem.innerHTML = value;
						}
					}

					elem = 0;

				// If using innerHTML throws an exception, use the fallback method
				} catch ( e ) {}
			}

			if ( elem ) {
				this.empty().append( value );
			}
		}, null, value, arguments.length );
	},

	replaceWith: function() {
		var ignored = [];

		// Make the changes, replacing each non-ignored context element with the new content
		return domManip( this, arguments, function( elem ) {
			var parent = this.parentNode;

			if ( jQuery.inArray( this, ignored ) < 0 ) {
				jQuery.cleanData( getAll( this ) );
				if ( parent ) {
					parent.replaceChild( elem, this );
				}
			}

		// Force callback invocation
		}, ignored );
	}
} );

jQuery.each( {
	appendTo: "append",
	prependTo: "prepend",
	insertBefore: "before",
	insertAfter: "after",
	replaceAll: "replaceWith"
}, function( name, original ) {
	jQuery.fn[ name ] = function( selector ) {
		var elems,
			ret = [],
			insert = jQuery( selector ),
			last = insert.length - 1,
			i = 0;

		for ( ; i <= last; i++ ) {
			elems = i === last ? this : this.clone( true );
			jQuery( insert[ i ] )[ original ]( elems );

			// Support: Android <=4.0 only, PhantomJS 1 only
			// .get() because push.apply(_, arraylike) throws on ancient WebKit
			push.apply( ret, elems.get() );
		}

		return this.pushStack( ret );
	};
} );
var rnumnonpx = new RegExp( "^(" + pnum + ")(?!px)[a-z%]+$", "i" );

var rcustomProp = /^--/;


var getStyles = function( elem ) {

		// Support: IE <=11 only, Firefox <=30 (trac-15098, trac-14150)
		// IE throws on elements created in popups
		// FF meanwhile throws on frame elements through "defaultView.getComputedStyle"
		var view = elem.ownerDocument.defaultView;

		if ( !view || !view.opener ) {
			view = window;
		}

		return view.getComputedStyle( elem );
	};

var swap = function( elem, options, callback ) {
	var ret, name,
		old = {};

	// Remember the old values, and insert the new ones
	for ( name in options ) {
		old[ name ] = elem.style[ name ];
		elem.style[ name ] = options[ name ];
	}

	ret = callback.call( elem );

	// Revert the old values
	for ( name in options ) {
		elem.style[ name ] = old[ name ];
	}

	return ret;
};


var rboxStyle = new RegExp( cssExpand.join( "|" ), "i" );

var whitespace = "[\\x20\\t\\r\\n\\f]";


var rtrimCSS = new RegExp(
	"^" + whitespace + "+|((?:^|[^\\\\])(?:\\\\.)*)" + whitespace + "+$",
	"g"
);




( function() {

	// Executing both pixelPosition & boxSizingReliable tests require only one layout
	// so they're executed at the same time to save the second computation.
	function computeStyleTests() {

		// This is a singleton, we need to execute it only once
		if ( !div ) {
			return;
		}

		container.style.cssText = "position:absolute;left:-11111px;width:60px;" +
			"margin-top:1px;padding:0;border:0";
		div.style.cssText =
			"position:relative;display:block;box-sizing:border-box;overflow:scroll;" +
			"margin:auto;border:1px;padding:1px;" +
			"width:60%;top:1%";
		documentElement.appendChild( container ).appendChild( div );

		var divStyle = window.getComputedStyle( div );
		pixelPositionVal = divStyle.top !== "1%";

		// Support: Android 4.0 - 4.3 only, Firefox <=3 - 44
		reliableMarginLeftVal = roundPixelMeasures( divStyle.marginLeft ) === 12;

		// Support: Android 4.0 - 4.3 only, Safari <=9.1 - 10.1, iOS <=7.0 - 9.3
		// Some styles come back with percentage values, even though they shouldn't
		div.style.right = "60%";
		pixelBoxStylesVal = roundPixelMeasures( divStyle.right ) === 36;

		// Support: IE 9 - 11 only
		// Detect misreporting of content dimensions for box-sizing:border-box elements
		boxSizingReliableVal = roundPixelMeasures( divStyle.width ) === 36;

		// Support: IE 9 only
		// Detect overflow:scroll screwiness (gh-3699)
		// Support: Chrome <=64
		// Don't get tricked when zoom affects offsetWidth (gh-4029)
		div.style.position = "absolute";
		scrollboxSizeVal = roundPixelMeasures( div.offsetWidth / 3 ) === 12;

		documentElement.removeChild( container );

		// Nullify the div so it wouldn't be stored in the memory and
		// it will also be a sign that checks already performed
		div = null;
	}

	function roundPixelMeasures( measure ) {
		return Math.round( parseFloat( measure ) );
	}

	var pixelPositionVal, boxSizingReliableVal, scrollboxSizeVal, pixelBoxStylesVal,
		reliableTrDimensionsVal, reliableMarginLeftVal,
		container = document.createElement( "div" ),
		div = document.createElement( "div" );

	// Finish early in limited (non-browser) environments
	if ( !div.style ) {
		return;
	}

	// Support: IE <=9 - 11 only
	// Style of cloned element affects source element cloned (trac-8908)
	div.style.backgroundClip = "content-box";
	div.cloneNode( true ).style.backgroundClip = "";
	support.clearCloneStyle = div.style.backgroundClip === "content-box";

	jQuery.extend( support, {
		boxSizingReliable: function() {
			computeStyleTests();
			return boxSizingReliableVal;
		},
		pixelBoxStyles: function() {
			computeStyleTests();
			return pixelBoxStylesVal;
		},
		pixelPosition: function() {
			computeStyleTests();
			return pixelPositionVal;
		},
		reliableMarginLeft: function() {
			computeStyleTests();
			return reliableMarginLeftVal;
		},
		scrollboxSize: function() {
			computeStyleTests();
			return scrollboxSizeVal;
		},

		// Support: IE 9 - 11+, Edge 15 - 18+
		// IE/Edge misreport `getComputedStyle` of table rows with width/height
		// set in CSS while `offset*` properties report correct values.
		// Behavior in IE 9 is more subtle than in newer versions & it passes
		// some versions of this test; make sure not to make it pass there!
		//
		// Support: Firefox 70+
		// Only Firefox includes border widths
		// in computed dimensions. (gh-4529)
		reliableTrDimensions: function() {
			var table, tr, trChild, trStyle;
			if ( reliableTrDimensionsVal == null ) {
				table = document.createElement( "table" );
				tr = document.createElement( "tr" );
				trChild = document.createElement( "div" );

				table.style.cssText = "position:absolute;left:-11111px;border-collapse:separate";
				tr.style.cssText = "border:1px solid";

				// Support: Chrome 86+
				// Height set through cssText does not get applied.
				// Computed height then comes back as 0.
				tr.style.height = "1px";
				trChild.style.height = "9px";

				// Support: Android 8 Chrome 86+
				// In our bodyBackground.html iframe,
				// display for all div elements is set to "inline",
				// which causes a problem only in Android 8 Chrome 86.
				// Ensuring the div is display: block
				// gets around this issue.
				trChild.style.display = "block";

				documentElement
					.appendChild( table )
					.appendChild( tr )
					.appendChild( trChild );

				trStyle = window.getComputedStyle( tr );
				reliableTrDimensionsVal = ( parseInt( trStyle.height, 10 ) +
					parseInt( trStyle.borderTopWidth, 10 ) +
					parseInt( trStyle.borderBottomWidth, 10 ) ) === tr.offsetHeight;

				documentElement.removeChild( table );
			}
			return reliableTrDimensionsVal;
		}
	} );
} )();


function curCSS( elem, name, computed ) {
	var width, minWidth, maxWidth, ret,
		isCustomProp = rcustomProp.test( name ),

		// Support: Firefox 51+
		// Retrieving style before computed somehow
		// fixes an issue with getting wrong values
		// on detached elements
		style = elem.style;

	computed = computed || getStyles( elem );

	// getPropertyValue is needed for:
	//   .css('filter') (IE 9 only, trac-12537)
	//   .css('--customProperty) (gh-3144)
	if ( computed ) {

		// Support: IE <=9 - 11+
		// IE only supports `"float"` in `getPropertyValue`; in computed styles
		// it's only available as `"cssFloat"`. We no longer modify properties
		// sent to `.css()` apart from camelCasing, so we need to check both.
		// Normally, this would create difference in behavior: if
		// `getPropertyValue` returns an empty string, the value returned
		// by `.css()` would be `undefined`. This is usually the case for
		// disconnected elements. However, in IE even disconnected elements
		// with no styles return `"none"` for `getPropertyValue( "float" )`
		ret = computed.getPropertyValue( name ) || computed[ name ];

		if ( isCustomProp && ret ) {

			// Support: Firefox 105+, Chrome <=105+
			// Spec requires trimming whitespace for custom properties (gh-4926).
			// Firefox only trims leading whitespace. Chrome just collapses
			// both leading & trailing whitespace to a single space.
			//
			// Fall back to `undefined` if empty string returned.
			// This collapses a missing definition with property defined
			// and set to an empty string but there's no standard API
			// allowing us to differentiate them without a performance penalty
			// and returning `undefined` aligns with older jQuery.
			//
			// rtrimCSS treats U+000D CARRIAGE RETURN and U+000C FORM FEED
			// as whitespace while CSS does not, but this is not a problem
			// because CSS preprocessing replaces them with U+000A LINE FEED
			// (which *is* CSS whitespace)
			// https://www.w3.org/TR/css-syntax-3/#input-preprocessing
			ret = ret.replace( rtrimCSS, "$1" ) || undefined;
		}

		if ( ret === "" && !isAttached( elem ) ) {
			ret = jQuery.style( elem, name );
		}

		// A tribute to the "awesome hack by Dean Edwards"
		// Android Browser returns percentage for some values,
		// but width seems to be reliably pixels.
		// This is against the CSSOM draft spec:
		// https://drafts.csswg.org/cssom/#resolved-values
		if ( !support.pixelBoxStyles() && rnumnonpx.test( ret ) && rboxStyle.test( name ) ) {

			// Remember the original values
			width = style.width;
			minWidth = style.minWidth;
			maxWidth = style.maxWidth;

			// Put in the new values to get a computed value out
			style.minWidth = style.maxWidth = style.width = ret;
			ret = computed.width;

			// Revert the changed values
			style.width = width;
			style.minWidth = minWidth;
			style.maxWidth = maxWidth;
		}
	}

	return ret !== undefined ?

		// Support: IE <=9 - 11 only
		// IE returns zIndex value as an integer.
		ret + "" :
		ret;
}


function addGetHookIf( conditionFn, hookFn ) {

	// Define the hook, we'll check on the first run if it's really needed.
	return {
		get: function() {
			if ( conditionFn() ) {

				// Hook not needed (or it's not possible to use it due
				// to missing dependency), remove it.
				delete this.get;
				return;
			}

			// Hook needed; redefine it so that the support test is not executed again.
			return ( this.get = hookFn ).apply( this, arguments );
		}
	};
}


var cssPrefixes = [ "Webkit", "Moz", "ms" ],
	emptyStyle = document.createElement( "div" ).style,
	vendorProps = {};

// Return a vendor-prefixed property or undefined
function vendorPropName( name ) {

	// Check for vendor prefixed names
	var capName = name[ 0 ].toUpperCase() + name.slice( 1 ),
		i = cssPrefixes.length;

	while ( i-- ) {
		name = cssPrefixes[ i ] + capName;
		if ( name in emptyStyle ) {
			return name;
		}
	}
}

// Return a potentially-mapped jQuery.cssProps or vendor prefixed property
function finalPropName( name ) {
	var final = jQuery.cssProps[ name ] || vendorProps[ name ];

	if ( final ) {
		return final;
	}
	if ( name in emptyStyle ) {
		return name;
	}
	return vendorProps[ name ] = vendorPropName( name ) || name;
}


var

	// Swappable if display is none or starts with table
	// except "table", "table-cell", or "table-caption"
	// See here for display values: https://developer.mozilla.org/en-US/docs/CSS/display
	rdisplayswap = /^(none|table(?!-c[ea]).+)/,
	cssShow = { position: "absolute", visibility: "hidden", display: "block" },
	cssNormalTransform = {
		letterSpacing: "0",
		fontWeight: "400"
	};

function setPositiveNumber( _elem, value, subtract ) {

	// Any relative (+/-) values have already been
	// normalized at this point
	var matches = rcssNum.exec( value );
	return matches ?

		// Guard against undefined "subtract", e.g., when used as in cssHooks
		Math.max( 0, matches[ 2 ] - ( subtract || 0 ) ) + ( matches[ 3 ] || "px" ) :
		value;
}

function boxModelAdjustment( elem, dimension, box, isBorderBox, styles, computedVal ) {
	var i = dimension === "width" ? 1 : 0,
		extra = 0,
		delta = 0;

	// Adjustment may not be necessary
	if ( box === ( isBorderBox ? "border" : "content" ) ) {
		return 0;
	}

	for ( ; i < 4; i += 2 ) {

		// Both box models exclude margin
		if ( box === "margin" ) {
			delta += jQuery.css( elem, box + cssExpand[ i ], true, styles );
		}

		// If we get here with a content-box, we're seeking "padding" or "border" or "margin"
		if ( !isBorderBox ) {

			// Add padding
			delta += jQuery.css( elem, "padding" + cssExpand[ i ], true, styles );

			// For "border" or "margin", add border
			if ( box !== "padding" ) {
				delta += jQuery.css( elem, "border" + cssExpand[ i ] + "Width", true, styles );

			// But still keep track of it otherwise
			} else {
				extra += jQuery.css( elem, "border" + cssExpand[ i ] + "Width", true, styles );
			}

		// If we get here with a border-box (content + padding + border), we're seeking "content" or
		// "padding" or "margin"
		} else {

			// For "content", subtract padding
			if ( box === "content" ) {
				delta -= jQuery.css( elem, "padding" + cssExpand[ i ], true, styles );
			}

			// For "content" or "padding", subtract border
			if ( box !== "margin" ) {
				delta -= jQuery.css( elem, "border" + cssExpand[ i ] + "Width", true, styles );
			}
		}
	}

	// Account for positive content-box scroll gutter when requested by providing computedVal
	if ( !isBorderBox && computedVal >= 0 ) {

		// offsetWidth/offsetHeight is a rounded sum of content, padding, scroll gutter, and border
		// Assuming integer scroll gutter, subtract the rest and round down
		delta += Math.max( 0, Math.ceil(
			elem[ "offset" + dimension[ 0 ].toUpperCase() + dimension.slice( 1 ) ] -
			computedVal -
			delta -
			extra -
			0.5

		// If offsetWidth/offsetHeight is unknown, then we can't determine content-box scroll gutter
		// Use an explicit zero to avoid NaN (gh-3964)
		) ) || 0;
	}

	return delta;
}

function getWidthOrHeight( elem, dimension, extra ) {

	// Start with computed style
	var styles = getStyles( elem ),

		// To avoid forcing a reflow, only fetch boxSizing if we need it (gh-4322).
		// Fake content-box until we know it's needed to know the true value.
		boxSizingNeeded = !support.boxSizingReliable() || extra,
		isBorderBox = boxSizingNeeded &&
			jQuery.css( elem, "boxSizing", false, styles ) === "border-box",
		valueIsBorderBox = isBorderBox,

		val = curCSS( elem, dimension, styles ),
		offsetProp = "offset" + dimension[ 0 ].toUpperCase() + dimension.slice( 1 );

	// Support: Firefox <=54
	// Return a confounding non-pixel value or feign ignorance, as appropriate.
	if ( rnumnonpx.test( val ) ) {
		if ( !extra ) {
			return val;
		}
		val = "auto";
	}


	// Support: IE 9 - 11 only
	// Use offsetWidth/offsetHeight for when box sizing is unreliable.
	// In those cases, the computed value can be trusted to be border-box.
	if ( ( !support.boxSizingReliable() && isBorderBox ||

		// Support: IE 10 - 11+, Edge 15 - 18+
		// IE/Edge misreport `getComputedStyle` of table rows with width/height
		// set in CSS while `offset*` properties report correct values.
		// Interestingly, in some cases IE 9 doesn't suffer from this issue.
		!support.reliableTrDimensions() && nodeName( elem, "tr" ) ||

		// Fall back to offsetWidth/offsetHeight when value is "auto"
		// This happens for inline elements with no explicit setting (gh-3571)
		val === "auto" ||

		// Support: Android <=4.1 - 4.3 only
		// Also use offsetWidth/offsetHeight for misreported inline dimensions (gh-3602)
		!parseFloat( val ) && jQuery.css( elem, "display", false, styles ) === "inline" ) &&

		// Make sure the element is visible & connected
		elem.getClientRects().length ) {

		isBorderBox = jQuery.css( elem, "boxSizing", false, styles ) === "border-box";

		// Where available, offsetWidth/offsetHeight approximate border box dimensions.
		// Where not available (e.g., SVG), assume unreliable box-sizing and interpret the
		// retrieved value as a content box dimension.
		valueIsBorderBox = offsetProp in elem;
		if ( valueIsBorderBox ) {
			val = elem[ offsetProp ];
		}
	}

	// Normalize "" and auto
	val = parseFloat( val ) || 0;

	// Adjust for the element's box model
	return ( val +
		boxModelAdjustment(
			elem,
			dimension,
			extra || ( isBorderBox ? "border" : "content" ),
			valueIsBorderBox,
			styles,

			// Provide the current computed size to request scroll gutter calculation (gh-3589)
			val
		)
	) + "px";
}

jQuery.extend( {

	// Add in style property hooks for overriding the default
	// behavior of getting and setting a style property
	cssHooks: {
		opacity: {
			get: function( elem, computed ) {
				if ( computed ) {

					// We should always get a number back from opacity
					var ret = curCSS( elem, "opacity" );
					return ret === "" ? "1" : ret;
				}
			}
		}
	},

	// Don't automatically add "px" to these possibly-unitless properties
	cssNumber: {
		"animationIterationCount": true,
		"columnCount": true,
		"fillOpacity": true,
		"flexGrow": true,
		"flexShrink": true,
		"fontWeight": true,
		"gridArea": true,
		"gridColumn": true,
		"gridColumnEnd": true,
		"gridColumnStart": true,
		"gridRow": true,
		"gridRowEnd": true,
		"gridRowStart": true,
		"lineHeight": true,
		"opacity": true,
		"order": true,
		"orphans": true,
		"widows": true,
		"zIndex": true,
		"zoom": true
	},

	// Add in properties whose names you wish to fix before
	// setting or getting the value
	cssProps: {},

	// Get and set the style property on a DOM Node
	style: function( elem, name, value, extra ) {

		// Don't set styles on text and comment nodes
		if ( !elem || elem.nodeType === 3 || elem.nodeType === 8 || !elem.style ) {
			return;
		}

		// Make sure that we're working with the right name
		var ret, type, hooks,
			origName = camelCase( name ),
			isCustomProp = rcustomProp.test( name ),
			style = elem.style;

		// Make sure that we're working with the right name. We don't
		// want to query the value if it is a CSS custom property
		// since they are user-defined.
		if ( !isCustomProp ) {
			name = finalPropName( origName );
		}

		// Gets hook for the prefixed version, then unprefixed version
		hooks = jQuery.cssHooks[ name ] || jQuery.cssHooks[ origName ];

		// Check if we're setting a value
		if ( value !== undefined ) {
			type = typeof value;

			// Convert "+=" or "-=" to relative numbers (trac-7345)
			if ( type === "string" && ( ret = rcssNum.exec( value ) ) && ret[ 1 ] ) {
				value = adjustCSS( elem, name, ret );

				// Fixes bug trac-9237
				type = "number";
			}

			// Make sure that null and NaN values aren't set (trac-7116)
			if ( value == null || value !== value ) {
				return;
			}

			// If a number was passed in, add the unit (except for certain CSS properties)
			// The isCustomProp check can be removed in jQuery 4.0 when we only auto-append
			// "px" to a few hardcoded values.
			if ( type === "number" && !isCustomProp ) {
				value += ret && ret[ 3 ] || ( jQuery.cssNumber[ origName ] ? "" : "px" );
			}

			// background-* props affect original clone's values
			if ( !support.clearCloneStyle && value === "" && name.indexOf( "background" ) === 0 ) {
				style[ name ] = "inherit";
			}

			// If a hook was provided, use that value, otherwise just set the specified value
			if ( !hooks || !( "set" in hooks ) ||
				( value = hooks.set( elem, value, extra ) ) !== undefined ) {

				if ( isCustomProp ) {
					style.setProperty( name, value );
				} else {
					style[ name ] = value;
				}
			}

		} else {

			// If a hook was provided get the non-computed value from there
			if ( hooks && "get" in hooks &&
				( ret = hooks.get( elem, false, extra ) ) !== undefined ) {

				return ret;
			}

			// Otherwise just get the value from the style object
			return style[ name ];
		}
	},

	css: function( elem, name, extra, styles ) {
		var val, num, hooks,
			origName = camelCase( name ),
			isCustomProp = rcustomProp.test( name );

		// Make sure that we're working with the right name. We don't
		// want to modify the value if it is a CSS custom property
		// since they are user-defined.
		if ( !isCustomProp ) {
			name = finalPropName( origName );
		}

		// Try prefixed name followed by the unprefixed name
		hooks = jQuery.cssHooks[ name ] || jQuery.cssHooks[ origName ];

		// If a hook was provided get the computed value from there
		if ( hooks && "get" in hooks ) {
			val = hooks.get( elem, true, extra );
		}

		// Otherwise, if a way to get the computed value exists, use that
		if ( val === undefined ) {
			val = curCSS( elem, name, styles );
		}

		// Convert "normal" to computed value
		if ( val === "normal" && name in cssNormalTransform ) {
			val = cssNormalTransform[ name ];
		}

		// Make numeric if forced or a qualifier was provided and val looks numeric
		if ( extra === "" || extra ) {
			num = parseFloat( val );
			return extra === true || isFinite( num ) ? num || 0 : val;
		}

		return val;
	}
} );

jQuery.each( [ "height", "width" ], function( _i, dimension ) {
	jQuery.cssHooks[ dimension ] = {
		get: function( elem, computed, extra ) {
			if ( computed ) {

				// Certain elements can have dimension info if we invisibly show them
				// but it must have a current display style that would benefit
				return rdisplayswap.test( jQuery.css( elem, "display" ) ) &&

					// Support: Safari 8+
					// Table columns in Safari have non-zero offsetWidth & zero
					// getBoundingClientRect().width unless display is changed.
					// Support: IE <=11 only
					// Running getBoundingClientRect on a disconnected node
					// in IE throws an error.
					( !elem.getClientRects().length || !elem.getBoundingClientRect().width ) ?
					swap( elem, cssShow, function() {
						return getWidthOrHeight( elem, dimension, extra );
					} ) :
					getWidthOrHeight( elem, dimension, extra );
			}
		},

		set: function( elem, value, extra ) {
			var matches,
				styles = getStyles( elem ),

				// Only read styles.position if the test has a chance to fail
				// to avoid forcing a reflow.
				scrollboxSizeBuggy = !support.scrollboxSize() &&
					styles.position === "absolute",

				// To avoid forcing a reflow, only fetch boxSizing if we need it (gh-3991)
				boxSizingNeeded = scrollboxSizeBuggy || extra,
				isBorderBox = boxSizingNeeded &&
					jQuery.css( elem, "boxSizing", false, styles ) === "border-box",
				subtract = extra ?
					boxModelAdjustment(
						elem,
						dimension,
						extra,
						isBorderBox,
						styles
					) :
					0;

			// Account for unreliable border-box dimensions by comparing offset* to computed and
			// faking a content-box to get border and padding (gh-3699)
			if ( isBorderBox && scrollboxSizeBuggy ) {
				subtract -= Math.ceil(
					elem[ "offset" + dimension[ 0 ].toUpperCase() + dimension.slice( 1 ) ] -
					parseFloat( styles[ dimension ] ) -
					boxModelAdjustment( elem, dimension, "border", false, styles ) -
					0.5
				);
			}

			// Convert to pixels if value adjustment is needed
			if ( subtract && ( matches = rcssNum.exec( value ) ) &&
				( matches[ 3 ] || "px" ) !== "px" ) {

				elem.style[ dimension ] = value;
				value = jQuery.css( elem, dimension );
			}

			return setPositiveNumber( elem, value, subtract );
		}
	};
} );

jQuery.cssHooks.marginLeft = addGetHookIf( support.reliableMarginLeft,
	function( elem, computed ) {
		if ( computed ) {
			return ( parseFloat( curCSS( elem, "marginLeft" ) ) ||
				elem.getBoundingClientRect().left -
					swap( elem, { marginLeft: 0 }, function() {
						return elem.getBoundingClientRect().left;
					} )
			) + "px";
		}
	}
);

// These hooks are used by animate to expand properties
jQuery.each( {
	margin: "",
	padding: "",
	border: "Width"
}, function( prefix, suffix ) {
	jQuery.cssHooks[ prefix + suffix ] = {
		expand: function( value ) {
			var i = 0,
				expanded = {},

				// Assumes a single number if not a string
				parts = typeof value === "string" ? value.split( " " ) : [ value ];

			for ( ; i < 4; i++ ) {
				expanded[ prefix + cssExpand[ i ] + suffix ] =
					parts[ i ] || parts[ i - 2 ] || parts[ 0 ];
			}

			return expanded;
		}
	};

	if ( prefix !== "margin" ) {
		jQuery.cssHooks[ prefix + suffix ].set = setPositiveNumber;
	}
} );

jQuery.fn.extend( {
	css: function( name, value ) {
		return access( this, function( elem, name, value ) {
			var styles, len,
				map = {},
				i = 0;

			if ( Array.isArray( name ) ) {
				styles = getStyles( elem );
				len = name.length;

				for ( ; i < len; i++ ) {
					map[ name[ i ] ] = jQuery.css( elem, name[ i ], false, styles );
				}

				return map;
			}

			return value !== undefined ?
				jQuery.style( elem, name, value ) :
				jQuery.css( elem, name );
		}, name, value, arguments.length > 1 );
	}
} );


function Tween( elem, options, prop, end, easing ) {
	return new Tween.prototype.init( elem, options, prop, end, easing );
}
jQuery.Tween = Tween;

Tween.prototype = {
	constructor: Tween,
	init: function( elem, options, prop, end, easing, unit ) {
		this.elem = elem;
		this.prop = prop;
		this.easing = easing || jQuery.easing._default;
		this.options = options;
		this.start = this.now = this.cur();
		this.end = end;
		this.unit = unit || ( jQuery.cssNumber[ prop ] ? "" : "px" );
	},
	cur: function() {
		var hooks = Tween.propHooks[ this.prop ];

		return hooks && hooks.get ?
			hooks.get( this ) :
			Tween.propHooks._default.get( this );
	},
	run: function( percent ) {
		var eased,
			hooks = Tween.propHooks[ this.prop ];

		if ( this.options.duration ) {
			this.pos = eased = jQuery.easing[ this.easing ](
				percent, this.options.duration * percent, 0, 1, this.options.duration
			);
		} else {
			this.pos = eased = percent;
		}
		this.now = ( this.end - this.start ) * eased + this.start;

		if ( this.options.step ) {
			this.options.step.call( this.elem, this.now, this );
		}

		if ( hooks && hooks.set ) {
			hooks.set( this );
		} else {
			Tween.propHooks._default.set( this );
		}
		return this;
	}
};

Tween.prototype.init.prototype = Tween.prototype;

Tween.propHooks = {
	_default: {
		get: function( tween ) {
			var result;

			// Use a property on the element directly when it is not a DOM element,
			// or when there is no matching style property that exists.
			if ( tween.elem.nodeType !== 1 ||
				tween.elem[ tween.prop ] != null && tween.elem.style[ tween.prop ] == null ) {
				return tween.elem[ tween.prop ];
			}

			// Passing an empty string as a 3rd parameter to .css will automatically
			// attempt a parseFloat and fallback to a string if the parse fails.
			// Simple values such as "10px" are parsed to Float;
			// complex values such as "rotate(1rad)" are returned as-is.
			result = jQuery.css( tween.elem, tween.prop, "" );

			// Empty strings, null, undefined and "auto" are converted to 0.
			return !result || result === "auto" ? 0 : result;
		},
		set: function( tween ) {

			// Use step hook for back compat.
			// Use cssHook if its there.
			// Use .style if available and use plain properties where available.
			if ( jQuery.fx.step[ tween.prop ] ) {
				jQuery.fx.step[ tween.prop ]( tween );
			} else if ( tween.elem.nodeType === 1 && (
				jQuery.cssHooks[ tween.prop ] ||
					tween.elem.style[ finalPropName( tween.prop ) ] != null ) ) {
				jQuery.style( tween.elem, tween.prop, tween.now + tween.unit );
			} else {
				tween.elem[ tween.prop ] = tween.now;
			}
		}
	}
};

// Support: IE <=9 only
// Panic based approach to setting things on disconnected nodes
Tween.propHooks.scrollTop = Tween.propHooks.scrollLeft = {
	set: function( tween ) {
		if ( tween.elem.nodeType && tween.elem.parentNode ) {
			tween.elem[ tween.prop ] = tween.now;
		}
	}
};

jQuery.easing = {
	linear: function( p ) {
		return p;
	},
	swing: function( p ) {
		return 0.5 - Math.cos( p * Math.PI ) / 2;
	},
	_default: "swing"
};

jQuery.fx = Tween.prototype.init;

// Back compat <1.8 extension point
jQuery.fx.step = {};




var
	fxNow, inProgress,
	rfxtypes = /^(?:toggle|show|hide)$/,
	rrun = /queueHooks$/;

function schedule() {
	if ( inProgress ) {
		if ( document.hidden === false && window.requestAnimationFrame ) {
			window.requestAnimationFrame( schedule );
		} else {
			window.setTimeout( schedule, jQuery.fx.interval );
		}

		jQuery.fx.tick();
	}
}

// Animations created synchronously will run synchronously
function createFxNow() {
	window.setTimeout( function() {
		fxNow = undefined;
	} );
	return ( fxNow = Date.now() );
}

// Generate parameters to create a standard animation
function genFx( type, includeWidth ) {
	var which,
		i = 0,
		attrs = { height: type };

	// If we include width, step value is 1 to do all cssExpand values,
	// otherwise step value is 2 to skip over Left and Right
	includeWidth = includeWidth ? 1 : 0;
	for ( ; i < 4; i += 2 - includeWidth ) {
		which = cssExpand[ i ];
		attrs[ "margin" + which ] = attrs[ "padding" + which ] = type;
	}

	if ( includeWidth ) {
		attrs.opacity = attrs.width = type;
	}

	return attrs;
}

function createTween( value, prop, animation ) {
	var tween,
		collection = ( Animation.tweeners[ prop ] || [] ).concat( Animation.tweeners[ "*" ] ),
		index = 0,
		length = collection.length;
	for ( ; index < length; index++ ) {
		if ( ( tween = collection[ index ].call( animation, prop, value ) ) ) {

			// We're done with this property
			return tween;
		}
	}
}

function defaultPrefilter( elem, props, opts ) {
	var prop, value, toggle, hooks, oldfire, propTween, restoreDisplay, display,
		isBox = "width" in props || "height" in props,
		anim = this,
		orig = {},
		style = elem.style,
		hidden = elem.nodeType && isHiddenWithinTree( elem ),
		dataShow = dataPriv.get( elem, "fxshow" );

	// Queue-skipping animations hijack the fx hooks
	if ( !opts.queue ) {
		hooks = jQuery._queueHooks( elem, "fx" );
		if ( hooks.unqueued == null ) {
			hooks.unqueued = 0;
			oldfire = hooks.empty.fire;
			hooks.empty.fire = function() {
				if ( !hooks.unqueued ) {
					oldfire();
				}
			};
		}
		hooks.unqueued++;

		anim.always( function() {

			// Ensure the complete handler is called before this completes
			anim.always( function() {
				hooks.unqueued--;
				if ( !jQuery.queue( elem, "fx" ).length ) {
					hooks.empty.fire();
				}
			} );
		} );
	}

	// Detect show/hide animations
	for ( prop in props ) {
		value = props[ prop ];
		if ( rfxtypes.test( value ) ) {
			delete props[ prop ];
			toggle = toggle || value === "toggle";
			if ( value === ( hidden ? "hide" : "show" ) ) {

				// Pretend to be hidden if this is a "show" and
				// there is still data from a stopped show/hide
				if ( value === "show" && dataShow && dataShow[ prop ] !== undefined ) {
					hidden = true;

				// Ignore all other no-op show/hide data
				} else {
					continue;
				}
			}
			orig[ prop ] = dataShow && dataShow[ prop ] || jQuery.style( elem, prop );
		}
	}

	// Bail out if this is a no-op like .hide().hide()
	propTween = !jQuery.isEmptyObject( props );
	if ( !propTween && jQuery.isEmptyObject( orig ) ) {
		return;
	}

	// Restrict "overflow" and "display" styles during box animations
	if ( isBox && elem.nodeType === 1 ) {

		// Support: IE <=9 - 11, Edge 12 - 15
		// Record all 3 overflow attributes because IE does not infer the shorthand
		// from identically-valued overflowX and overflowY and Edge just mirrors
		// the overflowX value there.
		opts.overflow = [ style.overflow, style.overflowX, style.overflowY ];

		// Identify a display type, preferring old show/hide data over the CSS cascade
		restoreDisplay = dataShow && dataShow.display;
		if ( restoreDisplay == null ) {
			restoreDisplay = dataPriv.get( elem, "display" );
		}
		display = jQuery.css( elem, "display" );
		if ( display === "none" ) {
			if ( restoreDisplay ) {
				display = restoreDisplay;
			} else {

				// Get nonempty value(s) by temporarily forcing visibility
				showHide( [ elem ], true );
				restoreDisplay = elem.style.display || restoreDisplay;
				display = jQuery.css( elem, "display" );
				showHide( [ elem ] );
			}
		}

		// Animate inline elements as inline-block
		if ( display === "inline" || display === "inline-block" && restoreDisplay != null ) {
			if ( jQuery.css( elem, "float" ) === "none" ) {

				// Restore the original display value at the end of pure show/hide animations
				if ( !propTween ) {
					anim.done( function() {
						style.display = restoreDisplay;
					} );
					if ( restoreDisplay == null ) {
						display = style.display;
						restoreDisplay = display === "none" ? "" : display;
					}
				}
				style.display = "inline-block";
			}
		}
	}

	if ( opts.overflow ) {
		style.overflow = "hidden";
		anim.always( function() {
			style.overflow = opts.overflow[ 0 ];
			style.overflowX = opts.overflow[ 1 ];
			style.overflowY = opts.overflow[ 2 ];
		} );
	}

	// Implement show/hide animations
	propTween = false;
	for ( prop in orig ) {

		// General show/hide setup for this element animation
		if ( !propTween ) {
			if ( dataShow ) {
				if ( "hidden" in dataShow ) {
					hidden = dataShow.hidden;
				}
			} else {
				dataShow = dataPriv.access( elem, "fxshow", { display: restoreDisplay } );
			}

			// Store hidden/visible for toggle so `.stop().toggle()` "reverses"
			if ( toggle ) {
				dataShow.hidden = !hidden;
			}

			// Show elements before animating them
			if ( hidden ) {
				showHide( [ elem ], true );
			}

			/* eslint-disable no-loop-func */

			anim.done( function() {

				/* eslint-enable no-loop-func */

				// The final step of a "hide" animation is actually hiding the element
				if ( !hidden ) {
					showHide( [ elem ] );
				}
				dataPriv.remove( elem, "fxshow" );
				for ( prop in orig ) {
					jQuery.style( elem, prop, orig[ prop ] );
				}
			} );
		}

		// Per-property setup
		propTween = createTween( hidden ? dataShow[ prop ] : 0, prop, anim );
		if ( !( prop in dataShow ) ) {
			dataShow[ prop ] = propTween.start;
			if ( hidden ) {
				propTween.end = propTween.start;
				propTween.start = 0;
			}
		}
	}
}

function propFilter( props, specialEasing ) {
	var index, name, easing, value, hooks;

	// camelCase, specialEasing and expand cssHook pass
	for ( index in props ) {
		name = camelCase( index );
		easing = specialEasing[ name ];
		value = props[ index ];
		if ( Array.isArray( value ) ) {
			easing = value[ 1 ];
			value = props[ index ] = value[ 0 ];
		}

		if ( index !== name ) {
			props[ name ] = value;
			delete props[ index ];
		}

		hooks = jQuery.cssHooks[ name ];
		if ( hooks && "expand" in hooks ) {
			value = hooks.expand( value );
			delete props[ name ];

			// Not quite $.extend, this won't overwrite existing keys.
			// Reusing 'index' because we have the correct "name"
			for ( index in value ) {
				if ( !( index in props ) ) {
					props[ index ] = value[ index ];
					specialEasing[ index ] = easing;
				}
			}
		} else {
			specialEasing[ name ] = easing;
		}
	}
}

function Animation( elem, properties, options ) {
	var result,
		stopped,
		index = 0,
		length = Animation.prefilters.length,
		deferred = jQuery.Deferred().always( function() {

			// Don't match elem in the :animated selector
			delete tick.elem;
		} ),
		tick = function() {
			if ( stopped ) {
				return false;
			}
			var currentTime = fxNow || createFxNow(),
				remaining = Math.max( 0, animation.startTime + animation.duration - currentTime ),

				// Support: Android 2.3 only
				// Archaic crash bug won't allow us to use `1 - ( 0.5 || 0 )` (trac-12497)
				temp = remaining / animation.duration || 0,
				percent = 1 - temp,
				index = 0,
				length = animation.tweens.length;

			for ( ; index < length; index++ ) {
				animation.tweens[ index ].run( percent );
			}

			deferred.notifyWith( elem, [ animation, percent, remaining ] );

			// If there's more to do, yield
			if ( percent < 1 && length ) {
				return remaining;
			}

			// If this was an empty animation, synthesize a final progress notification
			if ( !length ) {
				deferred.notifyWith( elem, [ animation, 1, 0 ] );
			}

			// Resolve the animation and report its conclusion
			deferred.resolveWith( elem, [ animation ] );
			return false;
		},
		animation = deferred.promise( {
			elem: elem,
			props: jQuery.extend( {}, properties ),
			opts: jQuery.extend( true, {
				specialEasing: {},
				easing: jQuery.easing._default
			}, options ),
			originalProperties: properties,
			originalOptions: options,
			startTime: fxNow || createFxNow(),
			duration: options.duration,
			tweens: [],
			createTween: function( prop, end ) {
				var tween = jQuery.Tween( elem, animation.opts, prop, end,
					animation.opts.specialEasing[ prop ] || animation.opts.easing );
				animation.tweens.push( tween );
				return tween;
			},
			stop: function( gotoEnd ) {
				var index = 0,

					// If we are going to the end, we want to run all the tweens
					// otherwise we skip this part
					length = gotoEnd ? animation.tweens.length : 0;
				if ( stopped ) {
					return this;
				}
				stopped = true;
				for ( ; index < length; index++ ) {
					animation.tweens[ index ].run( 1 );
				}

				// Resolve when we played the last frame; otherwise, reject
				if ( gotoEnd ) {
					deferred.notifyWith( elem, [ animation, 1, 0 ] );
					deferred.resolveWith( elem, [ animation, gotoEnd ] );
				} else {
					deferred.rejectWith( elem, [ animation, gotoEnd ] );
				}
				return this;
			}
		} ),
		props = animation.props;

	propFilter( props, animation.opts.specialEasing );

	for ( ; index < length; index++ ) {
		result = Animation.prefilters[ index ].call( animation, elem, props, animation.opts );
		if ( result ) {
			if ( isFunction( result.stop ) ) {
				jQuery._queueHooks( animation.elem, animation.opts.queue ).stop =
					result.stop.bind( result );
			}
			return result;
		}
	}

	jQuery.map( props, createTween, animation );

	if ( isFunction( animation.opts.start ) ) {
		animation.opts.start.call( elem, animation );
	}

	// Attach callbacks from options
	animation
		.progress( animation.opts.progress )
		.done( animation.opts.done, animation.opts.complete )
		.fail( animation.opts.fail )
		.always( animation.opts.always );

	jQuery.fx.timer(
		jQuery.extend( tick, {
			elem: elem,
			anim: animation,
			queue: animation.opts.queue
		} )
	);

	return animation;
}

jQuery.Animation = jQuery.extend( Animation, {

	tweeners: {
		"*": [ function( prop, value ) {
			var tween = this.createTween( prop, value );
			adjustCSS( tween.elem, prop, rcssNum.exec( value ), tween );
			return tween;
		} ]
	},

	tweener: function( props, callback ) {
		if ( isFunction( props ) ) {
			callback = props;
			props = [ "*" ];
		} else {
			props = props.match( rnothtmlwhite );
		}

		var prop,
			index = 0,
			length = props.length;

		for ( ; index < length; index++ ) {
			prop = props[ index ];
			Animation.tweeners[ prop ] = Animation.tweeners[ prop ] || [];
			Animation.tweeners[ prop ].unshift( callback );
		}
	},

	prefilters: [ defaultPrefilter ],

	prefilter: function( callback, prepend ) {
		if ( prepend ) {
			Animation.prefilters.unshift( callback );
		} else {
			Animation.prefilters.push( callback );
		}
	}
} );

jQuery.speed = function( speed, easing, fn ) {
	var opt = speed && typeof speed === "object" ? jQuery.extend( {}, speed ) : {
		complete: fn || !fn && easing ||
			isFunction( speed ) && speed,
		duration: speed,
		easing: fn && easing || easing && !isFunction( easing ) && easing
	};

	// Go to the end state if fx are off
	if ( jQuery.fx.off ) {
		opt.duration = 0;

	} else {
		if ( typeof opt.duration !== "number" ) {
			if ( opt.duration in jQuery.fx.speeds ) {
				opt.duration = jQuery.fx.speeds[ opt.duration ];

			} else {
				opt.duration = jQuery.fx.speeds._default;
			}
		}
	}

	// Normalize opt.queue - true/undefined/null -> "fx"
	if ( opt.queue == null || opt.queue === true ) {
		opt.queue = "fx";
	}

	// Queueing
	opt.old = opt.complete;

	opt.complete = function() {
		if ( isFunction( opt.old ) ) {
			opt.old.call( this );
		}

		if ( opt.queue ) {
			jQuery.dequeue( this, opt.queue );
		}
	};

	return opt;
};

jQuery.fn.extend( {
	fadeTo: function( speed, to, easing, callback ) {

		// Show any hidden elements after setting opacity to 0
		return this.filter( isHiddenWithinTree ).css( "opacity", 0 ).show()

			// Animate to the value specified
			.end().animate( { opacity: to }, speed, easing, callback );
	},
	animate: function( prop, speed, easing, callback ) {
		var empty = jQuery.isEmptyObject( prop ),
			optall = jQuery.speed( speed, easing, callback ),
			doAnimation = function() {

				// Operate on a copy of prop so per-property easing won't be lost
				var anim = Animation( this, jQuery.extend( {}, prop ), optall );

				// Empty animations, or finishing resolves immediately
				if ( empty || dataPriv.get( this, "finish" ) ) {
					anim.stop( true );
				}
			};

		doAnimation.finish = doAnimation;

		return empty || optall.queue === false ?
			this.each( doAnimation ) :
			this.queue( optall.queue, doAnimation );
	},
	stop: function( type, clearQueue, gotoEnd ) {
		var stopQueue = function( hooks ) {
			var stop = hooks.stop;
			delete hooks.stop;
			stop( gotoEnd );
		};

		if ( typeof type !== "string" ) {
			gotoEnd = clearQueue;
			clearQueue = type;
			type = undefined;
		}
		if ( clearQueue ) {
			this.queue( type || "fx", [] );
		}

		return this.each( function() {
			var dequeue = true,
				index = type != null && type + "queueHooks",
				timers = jQuery.timers,
				data = dataPriv.get( this );

			if ( index ) {
				if ( data[ index ] && data[ index ].stop ) {
					stopQueue( data[ index ] );
				}
			} else {
				for ( index in data ) {
					if ( data[ index ] && data[ index ].stop && rrun.test( index ) ) {
						stopQueue( data[ index ] );
					}
				}
			}

			for ( index = timers.length; index--; ) {
				if ( timers[ index ].elem === this &&
					( type == null || timers[ index ].queue === type ) ) {

					timers[ index ].anim.stop( gotoEnd );
					dequeue = false;
					timers.splice( index, 1 );
				}
			}

			// Start the next in the queue if the last step wasn't forced.
			// Timers currently will call their complete callbacks, which
			// will dequeue but only if they were gotoEnd.
			if ( dequeue || !gotoEnd ) {
				jQuery.dequeue( this, type );
			}
		} );
	},
	finish: function( type ) {
		if ( type !== false ) {
			type = type || "fx";
		}
		return this.each( function() {
			var index,
				data = dataPriv.get( this ),
				queue = data[ type + "queue" ],
				hooks = data[ type + "queueHooks" ],
				timers = jQuery.timers,
				length = queue ? queue.length : 0;

			// Enable finishing flag on private data
			data.finish = true;

			// Empty the queue first
			jQuery.queue( this, type, [] );

			if ( hooks && hooks.stop ) {
				hooks.stop.call( this, true );
			}

			// Look for any active animations, and finish them
			for ( index = timers.length; index--; ) {
				if ( timers[ index ].elem === this && timers[ index ].queue === type ) {
					timers[ index ].anim.stop( true );
					timers.splice( index, 1 );
				}
			}

			// Look for any animations in the old queue and finish them
			for ( index = 0; index < length; index++ ) {
				if ( queue[ index ] && queue[ index ].finish ) {
					queue[ index ].finish.call( this );
				}
			}

			// Turn off finishing flag
			delete data.finish;
		} );
	}
} );

jQuery.each( [ "toggle", "show", "hide" ], function( _i, name ) {
	var cssFn = jQuery.fn[ name ];
	jQuery.fn[ name ] = function( speed, easing, callback ) {
		return speed == null || typeof speed === "boolean" ?
			cssFn.apply( this, arguments ) :
			this.animate( genFx( name, true ), speed, easing, callback );
	};
} );

// Generate shortcuts for custom animations
jQuery.each( {
	slideDown: genFx( "show" ),
	slideUp: genFx( "hide" ),
	slideToggle: genFx( "toggle" ),
	fadeIn: { opacity: "show" },
	fadeOut: { opacity: "hide" },
	fadeToggle: { opacity: "toggle" }
}, function( name, props ) {
	jQuery.fn[ name ] = function( speed, easing, callback ) {
		return this.animate( props, speed, easing, callback );
	};
} );

jQuery.timers = [];
jQuery.fx.tick = function() {
	var timer,
		i = 0,
		timers = jQuery.timers;

	fxNow = Date.now();

	for ( ; i < timers.length; i++ ) {
		timer = timers[ i ];

		// Run the timer and safely remove it when done (allowing for external removal)
		if ( !timer() && timers[ i ] === timer ) {
			timers.splice( i--, 1 );
		}
	}

	if ( !timers.length ) {
		jQuery.fx.stop();
	}
	fxNow = undefined;
};

jQuery.fx.timer = function( timer ) {
	jQuery.timers.push( timer );
	jQuery.fx.start();
};

jQuery.fx.interval = 13;
jQuery.fx.start = function() {
	if ( inProgress ) {
		return;
	}

	inProgress = true;
	schedule();
};

jQuery.fx.stop = function() {
	inProgress = null;
};

jQuery.fx.speeds = {
	slow: 600,
	fast: 200,

	// Default speed
	_default: 400
};


// Based off of the plugin by Clint Helfers, with permission.
jQuery.fn.delay = function( time, type ) {
	time = jQuery.fx ? jQuery.fx.speeds[ time ] || time : time;
	type = type || "fx";

	return this.queue( type, function( next, hooks ) {
		var timeout = window.setTimeout( next, time );
		hooks.stop = function() {
			window.clearTimeout( timeout );
		};
	} );
};


( function() {
	var input = document.createElement( "input" ),
		select = document.createElement( "select" ),
		opt = select.appendChild( document.createElement( "option" ) );

	input.type = "checkbox";

	// Support: Android <=4.3 only
	// Default value for a checkbox should be "on"
	support.checkOn = input.value !== "";

	// Support: IE <=11 only
	// Must access selectedIndex to make default options select
	support.optSelected = opt.selected;

	// Support: IE <=11 only
	// An input loses its value after becoming a radio
	input = document.createElement( "input" );
	input.value = "t";
	input.type = "radio";
	support.radioValue = input.value === "t";
} )();


var boolHook,
	attrHandle = jQuery.expr.attrHandle;

jQuery.fn.extend( {
	attr: function( name, value ) {
		return access( this, jQuery.attr, name, value, arguments.length > 1 );
	},

	removeAttr: function( name ) {
		return this.each( function() {
			jQuery.removeAttr( this, name );
		} );
	}
} );

jQuery.extend( {
	attr: function( elem, name, value ) {
		var ret, hooks,
			nType = elem.nodeType;

		// Don't get/set attributes on text, comment and attribute nodes
		if ( nType === 3 || nType === 8 || nType === 2 ) {
			return;
		}

		// Fallback to prop when attributes are not supported
		if ( typeof elem.getAttribute === "undefined" ) {
			return jQuery.prop( elem, name, value );
		}

		// Attribute hooks are determined by the lowercase version
		// Grab necessary hook if one is defined
		if ( nType !== 1 || !jQuery.isXMLDoc( elem ) ) {
			hooks = jQuery.attrHooks[ name.toLowerCase() ] ||
				( jQuery.expr.match.bool.test( name ) ? boolHook : undefined );
		}

		if ( value !== undefined ) {
			if ( value === null ) {
				jQuery.removeAttr( elem, name );
				return;
			}

			if ( hooks && "set" in hooks &&
				( ret = hooks.set( elem, value, name ) ) !== undefined ) {
				return ret;
			}

			elem.setAttribute( name, value + "" );
			return value;
		}

		if ( hooks && "get" in hooks && ( ret = hooks.get( elem, name ) ) !== null ) {
			return ret;
		}

		ret = jQuery.find.attr( elem, name );

		// Non-existent attributes return null, we normalize to undefined
		return ret == null ? undefined : ret;
	},

	attrHooks: {
		type: {
			set: function( elem, value ) {
				if ( !support.radioValue && value === "radio" &&
					nodeName( elem, "input" ) ) {
					var val = elem.value;
					elem.setAttribute( "type", value );
					if ( val ) {
						elem.value = val;
					}
					return value;
				}
			}
		}
	},

	removeAttr: function( elem, value ) {
		var name,
			i = 0,

			// Attribute names can contain non-HTML whitespace characters
			// https://html.spec.whatwg.org/multipage/syntax.html#attributes-2
			attrNames = value && value.match( rnothtmlwhite );

		if ( attrNames && elem.nodeType === 1 ) {
			while ( ( name = attrNames[ i++ ] ) ) {
				elem.removeAttribute( name );
			}
		}
	}
} );

// Hooks for boolean attributes
boolHook = {
	set: function( elem, value, name ) {
		if ( value === false ) {

			// Remove boolean attributes when set to false
			jQuery.removeAttr( elem, name );
		} else {
			elem.setAttribute( name, name );
		}
		return name;
	}
};

jQuery.each( jQuery.expr.match.bool.source.match( /\w+/g ), function( _i, name ) {
	var getter = attrHandle[ name ] || jQuery.find.attr;

	attrHandle[ name ] = function( elem, name, isXML ) {
		var ret, handle,
			lowercaseName = name.toLowerCase();

		if ( !isXML ) {

			// Avoid an infinite loop by temporarily removing this function from the getter
			handle = attrHandle[ lowercaseName ];
			attrHandle[ lowercaseName ] = ret;
			ret = getter( elem, name, isXML ) != null ?
				lowercaseName :
				null;
			attrHandle[ lowercaseName ] = handle;
		}
		return ret;
	};
} );




var rfocusable = /^(?:input|select|textarea|button)$/i,
	rclickable = /^(?:a|area)$/i;

jQuery.fn.extend( {
	prop: function( name, value ) {
		return access( this, jQuery.prop, name, value, arguments.length > 1 );
	},

	removeProp: function( name ) {
		return this.each( function() {
			delete this[ jQuery.propFix[ name ] || name ];
		} );
	}
} );

jQuery.extend( {
	prop: function( elem, name, value ) {
		var ret, hooks,
			nType = elem.nodeType;

		// Don't get/set properties on text, comment and attribute nodes
		if ( nType === 3 || nType === 8 || nType === 2 ) {
			return;
		}

		if ( nType !== 1 || !jQuery.isXMLDoc( elem ) ) {

			// Fix name and attach hooks
			name = jQuery.propFix[ name ] || name;
			hooks = jQuery.propHooks[ name ];
		}

		if ( value !== undefined ) {
			if ( hooks && "set" in hooks &&
				( ret = hooks.set( elem, value, name ) ) !== undefined ) {
				return ret;
			}

			return ( elem[ name ] = value );
		}

		if ( hooks && "get" in hooks && ( ret = hooks.get( elem, name ) ) !== null ) {
			return ret;
		}

		return elem[ name ];
	},

	propHooks: {
		tabIndex: {
			get: function( elem ) {

				// Support: IE <=9 - 11 only
				// elem.tabIndex doesn't always return the
				// correct value when it hasn't been explicitly set
				// Use proper attribute retrieval (trac-12072)
				var tabindex = jQuery.find.attr( elem, "tabindex" );

				if ( tabindex ) {
					return parseInt( tabindex, 10 );
				}

				if (
					rfocusable.test( elem.nodeName ) ||
					rclickable.test( elem.nodeName ) &&
					elem.href
				) {
					return 0;
				}

				return -1;
			}
		}
	},

	propFix: {
		"for": "htmlFor",
		"class": "className"
	}
} );

// Support: IE <=11 only
// Accessing the selectedIndex property
// forces the browser to respect setting selected
// on the option
// The getter ensures a default option is selected
// when in an optgroup
// eslint rule "no-unused-expressions" is disabled for this code
// since it considers such accessions noop
if ( !support.optSelected ) {
	jQuery.propHooks.selected = {
		get: function( elem ) {

			/* eslint no-unused-expressions: "off" */

			var parent = elem.parentNode;
			if ( parent && parent.parentNode ) {
				parent.parentNode.selectedIndex;
			}
			return null;
		},
		set: function( elem ) {

			/* eslint no-unused-expressions: "off" */

			var parent = elem.parentNode;
			if ( parent ) {
				parent.selectedIndex;

				if ( parent.parentNode ) {
					parent.parentNode.selectedIndex;
				}
			}
		}
	};
}

jQuery.each( [
	"tabIndex",
	"readOnly",
	"maxLength",
	"cellSpacing",
	"cellPadding",
	"rowSpan",
	"colSpan",
	"useMap",
	"frameBorder",
	"contentEditable"
], function() {
	jQuery.propFix[ this.toLowerCase() ] = this;
} );




	// Strip and collapse whitespace according to HTML spec
	// https://infra.spec.whatwg.org/#strip-and-collapse-ascii-whitespace
	function stripAndCollapse( value ) {
		var tokens = value.match( rnothtmlwhite ) || [];
		return tokens.join( " " );
	}


function getClass( elem ) {
	return elem.getAttribute && elem.getAttribute( "class" ) || "";
}

function classesToArray( value ) {
	if ( Array.isArray( value ) ) {
		return value;
	}
	if ( typeof value === "string" ) {
		return value.match( rnothtmlwhite ) || [];
	}
	return [];
}

jQuery.fn.extend( {
	addClass: function( value ) {
		var classNames, cur, curValue, className, i, finalValue;

		if ( isFunction( value ) ) {
			return this.each( function( j ) {
				jQuery( this ).addClass( value.call( this, j, getClass( this ) ) );
			} );
		}

		classNames = classesToArray( value );

		if ( classNames.length ) {
			return this.each( function() {
				curValue = getClass( this );
				cur = this.nodeType === 1 && ( " " + stripAndCollapse( curValue ) + " " );

				if ( cur ) {
					for ( i = 0; i < classNames.length; i++ ) {
						className = classNames[ i ];
						if ( cur.indexOf( " " + className + " " ) < 0 ) {
							cur += className + " ";
						}
					}

					// Only assign if different to avoid unneeded rendering.
					finalValue = stripAndCollapse( cur );
					if ( curValue !== finalValue ) {
						this.setAttribute( "class", finalValue );
					}
				}
			} );
		}

		return this;
	},

	removeClass: function( value ) {
		var classNames, cur, curValue, className, i, finalValue;

		if ( isFunction( value ) ) {
			return this.each( function( j ) {
				jQuery( this ).removeClass( value.call( this, j, getClass( this ) ) );
			} );
		}

		if ( !arguments.length ) {
			return this.attr( "class", "" );
		}

		classNames = classesToArray( value );

		if ( classNames.length ) {
			return this.each( function() {
				curValue = getClass( this );

				// This expression is here for better compressibility (see addClass)
				cur = this.nodeType === 1 && ( " " + stripAndCollapse( curValue ) + " " );

				if ( cur ) {
					for ( i = 0; i < classNames.length; i++ ) {
						className = classNames[ i ];

						// Remove *all* instances
						while ( cur.indexOf( " " + className + " " ) > -1 ) {
							cur = cur.replace( " " + className + " ", " " );
						}
					}

					// Only assign if different to avoid unneeded rendering.
					finalValue = stripAndCollapse( cur );
					if ( curValue !== finalValue ) {
						this.setAttribute( "class", finalValue );
					}
				}
			} );
		}

		return this;
	},

	toggleClass: function( value, stateVal ) {
		var classNames, className, i, self,
			type = typeof value,
			isValidValue = type === "string" || Array.isArray( value );

		if ( isFunction( value ) ) {
			return this.each( function( i ) {
				jQuery( this ).toggleClass(
					value.call( this, i, getClass( this ), stateVal ),
					stateVal
				);
			} );
		}

		if ( typeof stateVal === "boolean" && isValidValue ) {
			return stateVal ? this.addClass( value ) : this.removeClass( value );
		}

		classNames = classesToArray( value );

		return this.each( function() {
			if ( isValidValue ) {

				// Toggle individual class names
				self = jQuery( this );

				for ( i = 0; i < classNames.length; i++ ) {
					className = classNames[ i ];

					// Check each className given, space separated list
					if ( self.hasClass( className ) ) {
						self.removeClass( className );
					} else {
						self.addClass( className );
					}
				}

			// Toggle whole class name
			} else if ( value === undefined || type === "boolean" ) {
				className = getClass( this );
				if ( className ) {

					// Store className if set
					dataPriv.set( this, "__className__", className );
				}

				// If the element has a class name or if we're passed `false`,
				// then remove the whole classname (if there was one, the above saved it).
				// Otherwise bring back whatever was previously saved (if anything),
				// falling back to the empty string if nothing was stored.
				if ( this.setAttribute ) {
					this.setAttribute( "class",
						className || value === false ?
							"" :
							dataPriv.get( this, "__className__" ) || ""
					);
				}
			}
		} );
	},

	hasClass: function( selector ) {
		var className, elem,
			i = 0;

		className = " " + selector + " ";
		while ( ( elem = this[ i++ ] ) ) {
			if ( elem.nodeType === 1 &&
				( " " + stripAndCollapse( getClass( elem ) ) + " " ).indexOf( className ) > -1 ) {
				return true;
			}
		}

		return false;
	}
} );




var rreturn = /\r/g;

jQuery.fn.extend( {
	val: function( value ) {
		var hooks, ret, valueIsFunction,
			elem = this[ 0 ];

		if ( !arguments.length ) {
			if ( elem ) {
				hooks = jQuery.valHooks[ elem.type ] ||
					jQuery.valHooks[ elem.nodeName.toLowerCase() ];

				if ( hooks &&
					"get" in hooks &&
					( ret = hooks.get( elem, "value" ) ) !== undefined
				) {
					return ret;
				}

				ret = elem.value;

				// Handle most common string cases
				if ( typeof ret === "string" ) {
					return ret.replace( rreturn, "" );
				}

				// Handle cases where value is null/undef or number
				return ret == null ? "" : ret;
			}

			return;
		}

		valueIsFunction = isFunction( value );

		return this.each( function( i ) {
			var val;

			if ( this.nodeType !== 1 ) {
				return;
			}

			if ( valueIsFunction ) {
				val = value.call( this, i, jQuery( this ).val() );
			} else {
				val = value;
			}

			// Treat null/undefined as ""; convert numbers to string
			if ( val == null ) {
				val = "";

			} else if ( typeof val === "number" ) {
				val += "";

			} else if ( Array.isArray( val ) ) {
				val = jQuery.map( val, function( value ) {
					return value == null ? "" : value + "";
				} );
			}

			hooks = jQuery.valHooks[ this.type ] || jQuery.valHooks[ this.nodeName.toLowerCase() ];

			// If set returns undefined, fall back to normal setting
			if ( !hooks || !( "set" in hooks ) || hooks.set( this, val, "value" ) === undefined ) {
				this.value = val;
			}
		} );
	}
} );

jQuery.extend( {
	valHooks: {
		option: {
			get: function( elem ) {

				var val = jQuery.find.attr( elem, "value" );
				return val != null ?
					val :

					// Support: IE <=10 - 11 only
					// option.text throws exceptions (trac-14686, trac-14858)
					// Strip and collapse whitespace
					// https://html.spec.whatwg.org/#strip-and-collapse-whitespace
					stripAndCollapse( jQuery.text( elem ) );
			}
		},
		select: {
			get: function( elem ) {
				var value, option, i,
					options = elem.options,
					index = elem.selectedIndex,
					one = elem.type === "select-one",
					values = one ? null : [],
					max = one ? index + 1 : options.length;

				if ( index < 0 ) {
					i = max;

				} else {
					i = one ? index : 0;
				}

				// Loop through all the selected options
				for ( ; i < max; i++ ) {
					option = options[ i ];

					// Support: IE <=9 only
					// IE8-9 doesn't update selected after form reset (trac-2551)
					if ( ( option.selected || i === index ) &&

							// Don't return options that are disabled or in a disabled optgroup
							!option.disabled &&
							( !option.parentNode.disabled ||
								!nodeName( option.parentNode, "optgroup" ) ) ) {

						// Get the specific value for the option
						value = jQuery( option ).val();

						// We don't need an array for one selects
						if ( one ) {
							return value;
						}

						// Multi-Selects return an array
						values.push( value );
					}
				}

				return values;
			},

			set: function( elem, value ) {
				var optionSet, option,
					options = elem.options,
					values = jQuery.makeArray( value ),
					i = options.length;

				while ( i-- ) {
					option = options[ i ];

					/* eslint-disable no-cond-assign */

					if ( option.selected =
						jQuery.inArray( jQuery.valHooks.option.get( option ), values ) > -1
					) {
						optionSet = true;
					}

					/* eslint-enable no-cond-assign */
				}

				// Force browsers to behave consistently when non-matching value is set
				if ( !optionSet ) {
					elem.selectedIndex = -1;
				}
				return values;
			}
		}
	}
} );

// Radios and checkboxes getter/setter
jQuery.each( [ "radio", "checkbox" ], function() {
	jQuery.valHooks[ this ] = {
		set: function( elem, value ) {
			if ( Array.isArray( value ) ) {
				return ( elem.checked = jQuery.inArray( jQuery( elem ).val(), value ) > -1 );
			}
		}
	};
	if ( !support.checkOn ) {
		jQuery.valHooks[ this ].get = function( elem ) {
			return elem.getAttribute( "value" ) === null ? "on" : elem.value;
		};
	}
} );




// Return jQuery for attributes-only inclusion


support.focusin = "onfocusin" in window;


var rfocusMorph = /^(?:focusinfocus|focusoutblur)$/,
	stopPropagationCallback = function( e ) {
		e.stopPropagation();
	};

jQuery.extend( jQuery.event, {

	trigger: function( event, data, elem, onlyHandlers ) {

		var i, cur, tmp, bubbleType, ontype, handle, special, lastElement,
			eventPath = [ elem || document ],
			type = hasOwn.call( event, "type" ) ? event.type : event,
			namespaces = hasOwn.call( event, "namespace" ) ? event.namespace.split( "." ) : [];

		cur = lastElement = tmp = elem = elem || document;

		// Don't do events on text and comment nodes
		if ( elem.nodeType === 3 || elem.nodeType === 8 ) {
			return;
		}

		// focus/blur morphs to focusin/out; ensure we're not firing them right now
		if ( rfocusMorph.test( type + jQuery.event.triggered ) ) {
			return;
		}

		if ( type.indexOf( "." ) > -1 ) {

			// Namespaced trigger; create a regexp to match event type in handle()
			namespaces = type.split( "." );
			type = namespaces.shift();
			namespaces.sort();
		}
		ontype = type.indexOf( ":" ) < 0 && "on" + type;

		// Caller can pass in a jQuery.Event object, Object, or just an event type string
		event = event[ jQuery.expando ] ?
			event :
			new jQuery.Event( type, typeof event === "object" && event );

		// Trigger bitmask: & 1 for native handlers; & 2 for jQuery (always true)
		event.isTrigger = onlyHandlers ? 2 : 3;
		event.namespace = namespaces.join( "." );
		event.rnamespace = event.namespace ?
			new RegExp( "(^|\\.)" + namespaces.join( "\\.(?:.*\\.|)" ) + "(\\.|$)" ) :
			null;

		// Clean up the event in case it is being reused
		event.result = undefined;
		if ( !event.target ) {
			event.target = elem;
		}

		// Clone any incoming data and prepend the event, creating the handler arg list
		data = data == null ?
			[ event ] :
			jQuery.makeArray( data, [ event ] );

		// Allow special events to draw outside the lines
		special = jQuery.event.special[ type ] || {};
		if ( !onlyHandlers && special.trigger && special.trigger.apply( elem, data ) === false ) {
			return;
		}

		// Determine event propagation path in advance, per W3C events spec (trac-9951)
		// Bubble up to document, then to window; watch for a global ownerDocument var (trac-9724)
		if ( !onlyHandlers && !special.noBubble && !isWindow( elem ) ) {

			bubbleType = special.delegateType || type;
			if ( !rfocusMorph.test( bubbleType + type ) ) {
				cur = cur.parentNode;
			}
			for ( ; cur; cur = cur.parentNode ) {
				eventPath.push( cur );
				tmp = cur;
			}

			// Only add window if we got to document (e.g., not plain obj or detached DOM)
			if ( tmp === ( elem.ownerDocument || document ) ) {
				eventPath.push( tmp.defaultView || tmp.parentWindow || window );
			}
		}

		// Fire handlers on the event path
		i = 0;
		while ( ( cur = eventPath[ i++ ] ) && !event.isPropagationStopped() ) {
			lastElement = cur;
			event.type = i > 1 ?
				bubbleType :
				special.bindType || type;

			// jQuery handler
			handle = ( dataPriv.get( cur, "events" ) || Object.create( null ) )[ event.type ] &&
				dataPriv.get( cur, "handle" );
			if ( handle ) {
				handle.apply( cur, data );
			}

			// Native handler
			handle = ontype && cur[ ontype ];
			if ( handle && handle.apply && acceptData( cur ) ) {
				event.result = handle.apply( cur, data );
				if ( event.result === false ) {
					event.preventDefault();
				}
			}
		}
		event.type = type;

		// If nobody prevented the default action, do it now
		if ( !onlyHandlers && !event.isDefaultPrevented() ) {

			if ( ( !special._default ||
				special._default.apply( eventPath.pop(), data ) === false ) &&
				acceptData( elem ) ) {

				// Call a native DOM method on the target with the same name as the event.
				// Don't do default actions on window, that's where global variables be (trac-6170)
				if ( ontype && isFunction( elem[ type ] ) && !isWindow( elem ) ) {

					// Don't re-trigger an onFOO event when we call its FOO() method
					tmp = elem[ ontype ];

					if ( tmp ) {
						elem[ ontype ] = null;
					}

					// Prevent re-triggering of the same event, since we already bubbled it above
					jQuery.event.triggered = type;

					if ( event.isPropagationStopped() ) {
						lastElement.addEventListener( type, stopPropagationCallback );
					}

					elem[ type ]();

					if ( event.isPropagationStopped() ) {
						lastElement.removeEventListener( type, stopPropagationCallback );
					}

					jQuery.event.triggered = undefined;

					if ( tmp ) {
						elem[ ontype ] = tmp;
					}
				}
			}
		}

		return event.result;
	},

	// Piggyback on a donor event to simulate a different one
	// Used only for `focus(in | out)` events
	simulate: function( type, elem, event ) {
		var e = jQuery.extend(
			new jQuery.Event(),
			event,
			{
				type: type,
				isSimulated: true
			}
		);

		jQuery.event.trigger( e, null, elem );
	}

} );

jQuery.fn.extend( {

	trigger: function( type, data ) {
		return this.each( function() {
			jQuery.event.trigger( type, data, this );
		} );
	},
	triggerHandler: function( type, data ) {
		var elem = this[ 0 ];
		if ( elem ) {
			return jQuery.event.trigger( type, data, elem, true );
		}
	}
} );


// Support: Firefox <=44
// Firefox doesn't have focus(in | out) events
// Related ticket - https://bugzilla.mozilla.org/show_bug.cgi?id=687787
//
// Support: Chrome <=48 - 49, Safari <=9.0 - 9.1
// focus(in | out) events fire after focus & blur events,
// which is spec violation - http://www.w3.org/TR/DOM-Level-3-Events/#events-focusevent-event-order
// Related ticket - https://bugs.chromium.org/p/chromium/issues/detail?id=449857
if ( !support.focusin ) {
	jQuery.each( { focus: "focusin", blur: "focusout" }, function( orig, fix ) {

		// Attach a single capturing handler on the document while someone wants focusin/focusout
		var handler = function( event ) {
			jQuery.event.simulate( fix, event.target, jQuery.event.fix( event ) );
		};

		jQuery.event.special[ fix ] = {
			setup: function() {

				// Handle: regular nodes (via `this.ownerDocument`), window
				// (via `this.document`) & document (via `this`).
				var doc = this.ownerDocument || this.document || this,
					attaches = dataPriv.access( doc, fix );

				if ( !attaches ) {
					doc.addEventListener( orig, handler, true );
				}
				dataPriv.access( doc, fix, ( attaches || 0 ) + 1 );
			},
			teardown: function() {
				var doc = this.ownerDocument || this.document || this,
					attaches = dataPriv.access( doc, fix ) - 1;

				if ( !attaches ) {
					doc.removeEventListener( orig, handler, true );
					dataPriv.remove( doc, fix );

				} else {
					dataPriv.access( doc, fix, attaches );
				}
			}
		};
	} );
}
var location = window.location;

var nonce = { guid: Date.now() };

var rquery = ( /\?/ );



// Cross-browser xml parsing
jQuery.parseXML = function( data ) {
	var xml, parserErrorElem;
	if ( !data || typeof data !== "string" ) {
		return null;
	}

	// Support: IE 9 - 11 only
	// IE throws on parseFromString with invalid input.
	try {
		xml = ( new window.DOMParser() ).parseFromString( data, "text/xml" );
	} catch ( e ) {}

	parserErrorElem = xml && xml.getElementsByTagName( "parsererror" )[ 0 ];
	if ( !xml || parserErrorElem ) {
		jQuery.error( "Invalid XML: " + (
			parserErrorElem ?
				jQuery.map( parserErrorElem.childNodes, function( el ) {
					return el.textContent;
				} ).join( "\n" ) :
				data
		) );
	}
	return xml;
};


var
	rbracket = /\[\]$/,
	rCRLF = /\r?\n/g,
	rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i,
	rsubmittable = /^(?:input|select|textarea|keygen)/i;

function buildParams( prefix, obj, traditional, add ) {
	var name;

	if ( Array.isArray( obj ) ) {

		// Serialize array item.
		jQuery.each( obj, function( i, v ) {
			if ( traditional || rbracket.test( prefix ) ) {

				// Treat each array item as a scalar.
				add( prefix, v );

			} else {

				// Item is non-scalar (array or object), encode its numeric index.
				buildParams(
					prefix + "[" + ( typeof v === "object" && v != null ? i : "" ) + "]",
					v,
					traditional,
					add
				);
			}
		} );

	} else if ( !traditional && toType( obj ) === "object" ) {

		// Serialize object item.
		for ( name in obj ) {
			buildParams( prefix + "[" + name + "]", obj[ name ], traditional, add );
		}

	} else {

		// Serialize scalar item.
		add( prefix, obj );
	}
}

// Serialize an array of form elements or a set of
// key/values into a query string
jQuery.param = function( a, traditional ) {
	var prefix,
		s = [],
		add = function( key, valueOrFunction ) {

			// If value is a function, invoke it and use its return value
			var value = isFunction( valueOrFunction ) ?
				valueOrFunction() :
				valueOrFunction;

			s[ s.length ] = encodeURIComponent( key ) + "=" +
				encodeURIComponent( value == null ? "" : value );
		};

	if ( a == null ) {
		return "";
	}

	// If an array was passed in, assume that it is an array of form elements.
	if ( Array.isArray( a ) || ( a.jquery && !jQuery.isPlainObject( a ) ) ) {

		// Serialize the form elements
		jQuery.each( a, function() {
			add( this.name, this.value );
		} );

	} else {

		// If traditional, encode the "old" way (the way 1.3.2 or older
		// did it), otherwise encode params recursively.
		for ( prefix in a ) {
			buildParams( prefix, a[ prefix ], traditional, add );
		}
	}

	// Return the resulting serialization
	return s.join( "&" );
};

jQuery.fn.extend( {
	serialize: function() {
		return jQuery.param( this.serializeArray() );
	},
	serializeArray: function() {
		return this.map( function() {

			// Can add propHook for "elements" to filter or add form elements
			var elements = jQuery.prop( this, "elements" );
			return elements ? jQuery.makeArray( elements ) : this;
		} ).filter( function() {
			var type = this.type;

			// Use .is( ":disabled" ) so that fieldset[disabled] works
			return this.name && !jQuery( this ).is( ":disabled" ) &&
				rsubmittable.test( this.nodeName ) && !rsubmitterTypes.test( type ) &&
				( this.checked || !rcheckableType.test( type ) );
		} ).map( function( _i, elem ) {
			var val = jQuery( this ).val();

			if ( val == null ) {
				return null;
			}

			if ( Array.isArray( val ) ) {
				return jQuery.map( val, function( val ) {
					return { name: elem.name, value: val.replace( rCRLF, "\r\n" ) };
				} );
			}

			return { name: elem.name, value: val.replace( rCRLF, "\r\n" ) };
		} ).get();
	}
} );


var
	r20 = /%20/g,
	rhash = /#.*$/,
	rantiCache = /([?&])_=[^&]*/,
	rheaders = /^(.*?):[ \t]*([^\r\n]*)$/mg,

	// trac-7653, trac-8125, trac-8152: local protocol detection
	rlocalProtocol = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
	rnoContent = /^(?:GET|HEAD)$/,
	rprotocol = /^\/\//,

	/* Prefilters
	 * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
	 * 2) These are called:
	 *    - BEFORE asking for a transport
	 *    - AFTER param serialization (s.data is a string if s.processData is true)
	 * 3) key is the dataType
	 * 4) the catchall symbol "*" can be used
	 * 5) execution will start with transport dataType and THEN continue down to "*" if needed
	 */
	prefilters = {},

	/* Transports bindings
	 * 1) key is the dataType
	 * 2) the catchall symbol "*" can be used
	 * 3) selection will start with transport dataType and THEN go to "*" if needed
	 */
	transports = {},

	// Avoid comment-prolog char sequence (trac-10098); must appease lint and evade compression
	allTypes = "*/".concat( "*" ),

	// Anchor tag for parsing the document origin
	originAnchor = document.createElement( "a" );

originAnchor.href = location.href;

// Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
function addToPrefiltersOrTransports( structure ) {

	// dataTypeExpression is optional and defaults to "*"
	return function( dataTypeExpression, func ) {

		if ( typeof dataTypeExpression !== "string" ) {
			func = dataTypeExpression;
			dataTypeExpression = "*";
		}

		var dataType,
			i = 0,
			dataTypes = dataTypeExpression.toLowerCase().match( rnothtmlwhite ) || [];

		if ( isFunction( func ) ) {

			// For each dataType in the dataTypeExpression
			while ( ( dataType = dataTypes[ i++ ] ) ) {

				// Prepend if requested
				if ( dataType[ 0 ] === "+" ) {
					dataType = dataType.slice( 1 ) || "*";
					( structure[ dataType ] = structure[ dataType ] || [] ).unshift( func );

				// Otherwise append
				} else {
					( structure[ dataType ] = structure[ dataType ] || [] ).push( func );
				}
			}
		}
	};
}

// Base inspection function for prefilters and transports
function inspectPrefiltersOrTransports( structure, options, originalOptions, jqXHR ) {

	var inspected = {},
		seekingTransport = ( structure === transports );

	function inspect( dataType ) {
		var selected;
		inspected[ dataType ] = true;
		jQuery.each( structure[ dataType ] || [], function( _, prefilterOrFactory ) {
			var dataTypeOrTransport = prefilterOrFactory( options, originalOptions, jqXHR );
			if ( typeof dataTypeOrTransport === "string" &&
				!seekingTransport && !inspected[ dataTypeOrTransport ] ) {

				options.dataTypes.unshift( dataTypeOrTransport );
				inspect( dataTypeOrTransport );
				return false;
			} else if ( seekingTransport ) {
				return !( selected = dataTypeOrTransport );
			}
		} );
		return selected;
	}

	return inspect( options.dataTypes[ 0 ] ) || !inspected[ "*" ] && inspect( "*" );
}

// A special extend for ajax options
// that takes "flat" options (not to be deep extended)
// Fixes trac-9887
function ajaxExtend( target, src ) {
	var key, deep,
		flatOptions = jQuery.ajaxSettings.flatOptions || {};

	for ( key in src ) {
		if ( src[ key ] !== undefined ) {
			( flatOptions[ key ] ? target : ( deep || ( deep = {} ) ) )[ key ] = src[ key ];
		}
	}
	if ( deep ) {
		jQuery.extend( true, target, deep );
	}

	return target;
}

/* Handles responses to an ajax request:
 * - finds the right dataType (mediates between content-type and expected dataType)
 * - returns the corresponding response
 */
function ajaxHandleResponses( s, jqXHR, responses ) {

	var ct, type, finalDataType, firstDataType,
		contents = s.contents,
		dataTypes = s.dataTypes;

	// Remove auto dataType and get content-type in the process
	while ( dataTypes[ 0 ] === "*" ) {
		dataTypes.shift();
		if ( ct === undefined ) {
			ct = s.mimeType || jqXHR.getResponseHeader( "Content-Type" );
		}
	}

	// Check if we're dealing with a known content-type
	if ( ct ) {
		for ( type in contents ) {
			if ( contents[ type ] && contents[ type ].test( ct ) ) {
				dataTypes.unshift( type );
				break;
			}
		}
	}

	// Check to see if we have a response for the expected dataType
	if ( dataTypes[ 0 ] in responses ) {
		finalDataType = dataTypes[ 0 ];
	} else {

		// Try convertible dataTypes
		for ( type in responses ) {
			if ( !dataTypes[ 0 ] || s.converters[ type + " " + dataTypes[ 0 ] ] ) {
				finalDataType = type;
				break;
			}
			if ( !firstDataType ) {
				firstDataType = type;
			}
		}

		// Or just use first one
		finalDataType = finalDataType || firstDataType;
	}

	// If we found a dataType
	// We add the dataType to the list if needed
	// and return the corresponding response
	if ( finalDataType ) {
		if ( finalDataType !== dataTypes[ 0 ] ) {
			dataTypes.unshift( finalDataType );
		}
		return responses[ finalDataType ];
	}
}

/* Chain conversions given the request and the original response
 * Also sets the responseXXX fields on the jqXHR instance
 */
function ajaxConvert( s, response, jqXHR, isSuccess ) {
	var conv2, current, conv, tmp, prev,
		converters = {},

		// Work with a copy of dataTypes in case we need to modify it for conversion
		dataTypes = s.dataTypes.slice();

	// Create converters map with lowercased keys
	if ( dataTypes[ 1 ] ) {
		for ( conv in s.converters ) {
			converters[ conv.toLowerCase() ] = s.converters[ conv ];
		}
	}

	current = dataTypes.shift();

	// Convert to each sequential dataType
	while ( current ) {

		if ( s.responseFields[ current ] ) {
			jqXHR[ s.responseFields[ current ] ] = response;
		}

		// Apply the dataFilter if provided
		if ( !prev && isSuccess && s.dataFilter ) {
			response = s.dataFilter( response, s.dataType );
		}

		prev = current;
		current = dataTypes.shift();

		if ( current ) {

			// There's only work to do if current dataType is non-auto
			if ( current === "*" ) {

				current = prev;

			// Convert response if prev dataType is non-auto and differs from current
			} else if ( prev !== "*" && prev !== current ) {

				// Seek a direct converter
				conv = converters[ prev + " " + current ] || converters[ "* " + current ];

				// If none found, seek a pair
				if ( !conv ) {
					for ( conv2 in converters ) {

						// If conv2 outputs current
						tmp = conv2.split( " " );
						if ( tmp[ 1 ] === current ) {

							// If prev can be converted to accepted input
							conv = converters[ prev + " " + tmp[ 0 ] ] ||
								converters[ "* " + tmp[ 0 ] ];
							if ( conv ) {

								// Condense equivalence converters
								if ( conv === true ) {
									conv = converters[ conv2 ];

								// Otherwise, insert the intermediate dataType
								} else if ( converters[ conv2 ] !== true ) {
									current = tmp[ 0 ];
									dataTypes.unshift( tmp[ 1 ] );
								}
								break;
							}
						}
					}
				}

				// Apply converter (if not an equivalence)
				if ( conv !== true ) {

					// Unless errors are allowed to bubble, catch and return them
					if ( conv && s.throws ) {
						response = conv( response );
					} else {
						try {
							response = conv( response );
						} catch ( e ) {
							return {
								state: "parsererror",
								error: conv ? e : "No conversion from " + prev + " to " + current
							};
						}
					}
				}
			}
		}
	}

	return { state: "success", data: response };
}

jQuery.extend( {

	// Counter for holding the number of active queries
	active: 0,

	// Last-Modified header cache for next request
	lastModified: {},
	etag: {},

	ajaxSettings: {
		url: location.href,
		type: "GET",
		isLocal: rlocalProtocol.test( location.protocol ),
		global: true,
		processData: true,
		async: true,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",

		/*
		timeout: 0,
		data: null,
		dataType: null,
		username: null,
		password: null,
		cache: null,
		throws: false,
		traditional: false,
		headers: {},
		*/

		accepts: {
			"*": allTypes,
			text: "text/plain",
			html: "text/html",
			xml: "application/xml, text/xml",
			json: "application/json, text/javascript"
		},

		contents: {
			xml: /\bxml\b/,
			html: /\bhtml/,
			json: /\bjson\b/
		},

		responseFields: {
			xml: "responseXML",
			text: "responseText",
			json: "responseJSON"
		},

		// Data converters
		// Keys separate source (or catchall "*") and destination types with a single space
		converters: {

			// Convert anything to text
			"* text": String,

			// Text to html (true = no transformation)
			"text html": true,

			// Evaluate text as a json expression
			"text json": JSON.parse,

			// Parse text as xml
			"text xml": jQuery.parseXML
		},

		// For options that shouldn't be deep extended:
		// you can add your own custom options here if
		// and when you create one that shouldn't be
		// deep extended (see ajaxExtend)
		flatOptions: {
			url: true,
			context: true
		}
	},

	// Creates a full fledged settings object into target
	// with both ajaxSettings and settings fields.
	// If target is omitted, writes into ajaxSettings.
	ajaxSetup: function( target, settings ) {
		return settings ?

			// Building a settings object
			ajaxExtend( ajaxExtend( target, jQuery.ajaxSettings ), settings ) :

			// Extending ajaxSettings
			ajaxExtend( jQuery.ajaxSettings, target );
	},

	ajaxPrefilter: addToPrefiltersOrTransports( prefilters ),
	ajaxTransport: addToPrefiltersOrTransports( transports ),

	// Main method
	ajax: function( url, options ) {

		// If url is an object, simulate pre-1.5 signature
		if ( typeof url === "object" ) {
			options = url;
			url = undefined;
		}

		// Force options to be an object
		options = options || {};

		var transport,

			// URL without anti-cache param
			cacheURL,

			// Response headers
			responseHeadersString,
			responseHeaders,

			// timeout handle
			timeoutTimer,

			// Url cleanup var
			urlAnchor,

			// Request state (becomes false upon send and true upon completion)
			completed,

			// To know if global events are to be dispatched
			fireGlobals,

			// Loop variable
			i,

			// uncached part of the url
			uncached,

			// Create the final options object
			s = jQuery.ajaxSetup( {}, options ),

			// Callbacks context
			callbackContext = s.context || s,

			// Context for global events is callbackContext if it is a DOM node or jQuery collection
			globalEventContext = s.context &&
				( callbackContext.nodeType || callbackContext.jquery ) ?
				jQuery( callbackContext ) :
				jQuery.event,

			// Deferreds
			deferred = jQuery.Deferred(),
			completeDeferred = jQuery.Callbacks( "once memory" ),

			// Status-dependent callbacks
			statusCode = s.statusCode || {},

			// Headers (they are sent all at once)
			requestHeaders = {},
			requestHeadersNames = {},

			// Default abort message
			strAbort = "canceled",

			// Fake xhr
			jqXHR = {
				readyState: 0,

				// Builds headers hashtable if needed
				getResponseHeader: function( key ) {
					var match;
					if ( completed ) {
						if ( !responseHeaders ) {
							responseHeaders = {};
							while ( ( match = rheaders.exec( responseHeadersString ) ) ) {
								responseHeaders[ match[ 1 ].toLowerCase() + " " ] =
									( responseHeaders[ match[ 1 ].toLowerCase() + " " ] || [] )
										.concat( match[ 2 ] );
							}
						}
						match = responseHeaders[ key.toLowerCase() + " " ];
					}
					return match == null ? null : match.join( ", " );
				},

				// Raw string
				getAllResponseHeaders: function() {
					return completed ? responseHeadersString : null;
				},

				// Caches the header
				setRequestHeader: function( name, value ) {
					if ( completed == null ) {
						name = requestHeadersNames[ name.toLowerCase() ] =
							requestHeadersNames[ name.toLowerCase() ] || name;
						requestHeaders[ name ] = value;
					}
					return this;
				},

				// Overrides response content-type header
				overrideMimeType: function( type ) {
					if ( completed == null ) {
						s.mimeType = type;
					}
					return this;
				},

				// Status-dependent callbacks
				statusCode: function( map ) {
					var code;
					if ( map ) {
						if ( completed ) {

							// Execute the appropriate callbacks
							jqXHR.always( map[ jqXHR.status ] );
						} else {

							// Lazy-add the new callbacks in a way that preserves old ones
							for ( code in map ) {
								statusCode[ code ] = [ statusCode[ code ], map[ code ] ];
							}
						}
					}
					return this;
				},

				// Cancel the request
				abort: function( statusText ) {
					var finalText = statusText || strAbort;
					if ( transport ) {
						transport.abort( finalText );
					}
					done( 0, finalText );
					return this;
				}
			};

		// Attach deferreds
		deferred.promise( jqXHR );

		// Add protocol if not provided (prefilters might expect it)
		// Handle falsy url in the settings object (trac-10093: consistency with old signature)
		// We also use the url parameter if available
		s.url = ( ( url || s.url || location.href ) + "" )
			.replace( rprotocol, location.protocol + "//" );

		// Alias method option to type as per ticket trac-12004
		s.type = options.method || options.type || s.method || s.type;

		// Extract dataTypes list
		s.dataTypes = ( s.dataType || "*" ).toLowerCase().match( rnothtmlwhite ) || [ "" ];

		// A cross-domain request is in order when the origin doesn't match the current origin.
		if ( s.crossDomain == null ) {
			urlAnchor = document.createElement( "a" );

			// Support: IE <=8 - 11, Edge 12 - 15
			// IE throws exception on accessing the href property if url is malformed,
			// e.g. http://example.com:80x/
			try {
				urlAnchor.href = s.url;

				// Support: IE <=8 - 11 only
				// Anchor's host property isn't correctly set when s.url is relative
				urlAnchor.href = urlAnchor.href;
				s.crossDomain = originAnchor.protocol + "//" + originAnchor.host !==
					urlAnchor.protocol + "//" + urlAnchor.host;
			} catch ( e ) {

				// If there is an error parsing the URL, assume it is crossDomain,
				// it can be rejected by the transport if it is invalid
				s.crossDomain = true;
			}
		}

		// Convert data if not already a string
		if ( s.data && s.processData && typeof s.data !== "string" ) {
			s.data = jQuery.param( s.data, s.traditional );
		}

		// Apply prefilters
		inspectPrefiltersOrTransports( prefilters, s, options, jqXHR );

		// If request was aborted inside a prefilter, stop there
		if ( completed ) {
			return jqXHR;
		}

		// We can fire global events as of now if asked to
		// Don't fire events if jQuery.event is undefined in an AMD-usage scenario (trac-15118)
		fireGlobals = jQuery.event && s.global;

		// Watch for a new set of requests
		if ( fireGlobals && jQuery.active++ === 0 ) {
			jQuery.event.trigger( "ajaxStart" );
		}

		// Uppercase the type
		s.type = s.type.toUpperCase();

		// Determine if request has content
		s.hasContent = !rnoContent.test( s.type );

		// Save the URL in case we're toying with the If-Modified-Since
		// and/or If-None-Match header later on
		// Remove hash to simplify url manipulation
		cacheURL = s.url.replace( rhash, "" );

		// More options handling for requests with no content
		if ( !s.hasContent ) {

			// Remember the hash so we can put it back
			uncached = s.url.slice( cacheURL.length );

			// If data is available and should be processed, append data to url
			if ( s.data && ( s.processData || typeof s.data === "string" ) ) {
				cacheURL += ( rquery.test( cacheURL ) ? "&" : "?" ) + s.data;

				// trac-9682: remove data so that it's not used in an eventual retry
				delete s.data;
			}

			// Add or update anti-cache param if needed
			if ( s.cache === false ) {
				cacheURL = cacheURL.replace( rantiCache, "$1" );
				uncached = ( rquery.test( cacheURL ) ? "&" : "?" ) + "_=" + ( nonce.guid++ ) +
					uncached;
			}

			// Put hash and anti-cache on the URL that will be requested (gh-1732)
			s.url = cacheURL + uncached;

		// Change '%20' to '+' if this is encoded form body content (gh-2658)
		} else if ( s.data && s.processData &&
			( s.contentType || "" ).indexOf( "application/x-www-form-urlencoded" ) === 0 ) {
			s.data = s.data.replace( r20, "+" );
		}

		// Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
		if ( s.ifModified ) {
			if ( jQuery.lastModified[ cacheURL ] ) {
				jqXHR.setRequestHeader( "If-Modified-Since", jQuery.lastModified[ cacheURL ] );
			}
			if ( jQuery.etag[ cacheURL ] ) {
				jqXHR.setRequestHeader( "If-None-Match", jQuery.etag[ cacheURL ] );
			}
		}

		// Set the correct header, if data is being sent
		if ( s.data && s.hasContent && s.contentType !== false || options.contentType ) {
			jqXHR.setRequestHeader( "Content-Type", s.contentType );
		}

		// Set the Accepts header for the server, depending on the dataType
		jqXHR.setRequestHeader(
			"Accept",
			s.dataTypes[ 0 ] && s.accepts[ s.dataTypes[ 0 ] ] ?
				s.accepts[ s.dataTypes[ 0 ] ] +
					( s.dataTypes[ 0 ] !== "*" ? ", " + allTypes + "; q=0.01" : "" ) :
				s.accepts[ "*" ]
		);

		// Check for headers option
		for ( i in s.headers ) {
			jqXHR.setRequestHeader( i, s.headers[ i ] );
		}

		// Allow custom headers/mimetypes and early abort
		if ( s.beforeSend &&
			( s.beforeSend.call( callbackContext, jqXHR, s ) === false || completed ) ) {

			// Abort if not done already and return
			return jqXHR.abort();
		}

		// Aborting is no longer a cancellation
		strAbort = "abort";

		// Install callbacks on deferreds
		completeDeferred.add( s.complete );
		jqXHR.done( s.success );
		jqXHR.fail( s.error );

		// Get transport
		transport = inspectPrefiltersOrTransports( transports, s, options, jqXHR );

		// If no transport, we auto-abort
		if ( !transport ) {
			done( -1, "No Transport" );
		} else {
			jqXHR.readyState = 1;

			// Send global event
			if ( fireGlobals ) {
				globalEventContext.trigger( "ajaxSend", [ jqXHR, s ] );
			}

			// If request was aborted inside ajaxSend, stop there
			if ( completed ) {
				return jqXHR;
			}

			// Timeout
			if ( s.async && s.timeout > 0 ) {
				timeoutTimer = window.setTimeout( function() {
					jqXHR.abort( "timeout" );
				}, s.timeout );
			}

			try {
				completed = false;
				transport.send( requestHeaders, done );
			} catch ( e ) {

				// Rethrow post-completion exceptions
				if ( completed ) {
					throw e;
				}

				// Propagate others as results
				done( -1, e );
			}
		}

		// Callback for when everything is done
		function done( status, nativeStatusText, responses, headers ) {
			var isSuccess, success, error, response, modified,
				statusText = nativeStatusText;

			// Ignore repeat invocations
			if ( completed ) {
				return;
			}

			completed = true;

			// Clear timeout if it exists
			if ( timeoutTimer ) {
				window.clearTimeout( timeoutTimer );
			}

			// Dereference transport for early garbage collection
			// (no matter how long the jqXHR object will be used)
			transport = undefined;

			// Cache response headers
			responseHeadersString = headers || "";

			// Set readyState
			jqXHR.readyState = status > 0 ? 4 : 0;

			// Determine if successful
			isSuccess = status >= 200 && status < 300 || status === 304;

			// Get response data
			if ( responses ) {
				response = ajaxHandleResponses( s, jqXHR, responses );
			}

			// Use a noop converter for missing script but not if jsonp
			if ( !isSuccess &&
				jQuery.inArray( "script", s.dataTypes ) > -1 &&
				jQuery.inArray( "json", s.dataTypes ) < 0 ) {
				s.converters[ "text script" ] = function() {};
			}

			// Convert no matter what (that way responseXXX fields are always set)
			response = ajaxConvert( s, response, jqXHR, isSuccess );

			// If successful, handle type chaining
			if ( isSuccess ) {

				// Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
				if ( s.ifModified ) {
					modified = jqXHR.getResponseHeader( "Last-Modified" );
					if ( modified ) {
						jQuery.lastModified[ cacheURL ] = modified;
					}
					modified = jqXHR.getResponseHeader( "etag" );
					if ( modified ) {
						jQuery.etag[ cacheURL ] = modified;
					}
				}

				// if no content
				if ( status === 204 || s.type === "HEAD" ) {
					statusText = "nocontent";

				// if not modified
				} else if ( status === 304 ) {
					statusText = "notmodified";

				// If we have data, let's convert it
				} else {
					statusText = response.state;
					success = response.data;
					error = response.error;
					isSuccess = !error;
				}
			} else {

				// Extract error from statusText and normalize for non-aborts
				error = statusText;
				if ( status || !statusText ) {
					statusText = "error";
					if ( status < 0 ) {
						status = 0;
					}
				}
			}

			// Set data for the fake xhr object
			jqXHR.status = status;
			jqXHR.statusText = ( nativeStatusText || statusText ) + "";

			// Success/Error
			if ( isSuccess ) {
				deferred.resolveWith( callbackContext, [ success, statusText, jqXHR ] );
			} else {
				deferred.rejectWith( callbackContext, [ jqXHR, statusText, error ] );
			}

			// Status-dependent callbacks
			jqXHR.statusCode( statusCode );
			statusCode = undefined;

			if ( fireGlobals ) {
				globalEventContext.trigger( isSuccess ? "ajaxSuccess" : "ajaxError",
					[ jqXHR, s, isSuccess ? success : error ] );
			}

			// Complete
			completeDeferred.fireWith( callbackContext, [ jqXHR, statusText ] );

			if ( fireGlobals ) {
				globalEventContext.trigger( "ajaxComplete", [ jqXHR, s ] );

				// Handle the global AJAX counter
				if ( !( --jQuery.active ) ) {
					jQuery.event.trigger( "ajaxStop" );
				}
			}
		}

		return jqXHR;
	},

	getJSON: function( url, data, callback ) {
		return jQuery.get( url, data, callback, "json" );
	},

	getScript: function( url, callback ) {
		return jQuery.get( url, undefined, callback, "script" );
	}
} );

jQuery.each( [ "get", "post" ], function( _i, method ) {
	jQuery[ method ] = function( url, data, callback, type ) {

		// Shift arguments if data argument was omitted
		if ( isFunction( data ) ) {
			type = type || callback;
			callback = data;
			data = undefined;
		}

		// The url can be an options object (which then must have .url)
		return jQuery.ajax( jQuery.extend( {
			url: url,
			type: method,
			dataType: type,
			data: data,
			success: callback
		}, jQuery.isPlainObject( url ) && url ) );
	};
} );

jQuery.ajaxPrefilter( function( s ) {
	var i;
	for ( i in s.headers ) {
		if ( i.toLowerCase() === "content-type" ) {
			s.contentType = s.headers[ i ] || "";
		}
	}
} );


jQuery._evalUrl = function( url, options, doc ) {
	return jQuery.ajax( {
		url: url,

		// Make this explicit, since user can override this through ajaxSetup (trac-11264)
		type: "GET",
		dataType: "script",
		cache: true,
		async: false,
		global: false,

		// Only evaluate the response if it is successful (gh-4126)
		// dataFilter is not invoked for failure responses, so using it instead
		// of the default converter is kludgy but it works.
		converters: {
			"text script": function() {}
		},
		dataFilter: function( response ) {
			jQuery.globalEval( response, options, doc );
		}
	} );
};


jQuery.fn.extend( {
	wrapAll: function( html ) {
		var wrap;

		if ( this[ 0 ] ) {
			if ( isFunction( html ) ) {
				html = html.call( this[ 0 ] );
			}

			// The elements to wrap the target around
			wrap = jQuery( html, this[ 0 ].ownerDocument ).eq( 0 ).clone( true );

			if ( this[ 0 ].parentNode ) {
				wrap.insertBefore( this[ 0 ] );
			}

			wrap.map( function() {
				var elem = this;

				while ( elem.firstElementChild ) {
					elem = elem.firstElementChild;
				}

				return elem;
			} ).append( this );
		}

		return this;
	},

	wrapInner: function( html ) {
		if ( isFunction( html ) ) {
			return this.each( function( i ) {
				jQuery( this ).wrapInner( html.call( this, i ) );
			} );
		}

		return this.each( function() {
			var self = jQuery( this ),
				contents = self.contents();

			if ( contents.length ) {
				contents.wrapAll( html );

			} else {
				self.append( html );
			}
		} );
	},

	wrap: function( html ) {
		var htmlIsFunction = isFunction( html );

		return this.each( function( i ) {
			jQuery( this ).wrapAll( htmlIsFunction ? html.call( this, i ) : html );
		} );
	},

	unwrap: function( selector ) {
		this.parent( selector ).not( "body" ).each( function() {
			jQuery( this ).replaceWith( this.childNodes );
		} );
		return this;
	}
} );


jQuery.expr.pseudos.hidden = function( elem ) {
	return !jQuery.expr.pseudos.visible( elem );
};
jQuery.expr.pseudos.visible = function( elem ) {
	return !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length );
};




jQuery.ajaxSettings.xhr = function() {
	try {
		return new window.XMLHttpRequest();
	} catch ( e ) {}
};

var xhrSuccessStatus = {

		// File protocol always yields status code 0, assume 200
		0: 200,

		// Support: IE <=9 only
		// trac-1450: sometimes IE returns 1223 when it should be 204
		1223: 204
	},
	xhrSupported = jQuery.ajaxSettings.xhr();

support.cors = !!xhrSupported && ( "withCredentials" in xhrSupported );
support.ajax = xhrSupported = !!xhrSupported;

jQuery.ajaxTransport( function( options ) {
	var callback, errorCallback;

	// Cross domain only allowed if supported through XMLHttpRequest
	if ( support.cors || xhrSupported && !options.crossDomain ) {
		return {
			send: function( headers, complete ) {
				var i,
					xhr = options.xhr();

				xhr.open(
					options.type,
					options.url,
					options.async,
					options.username,
					options.password
				);

				// Apply custom fields if provided
				if ( options.xhrFields ) {
					for ( i in options.xhrFields ) {
						xhr[ i ] = options.xhrFields[ i ];
					}
				}

				// Override mime type if needed
				if ( options.mimeType && xhr.overrideMimeType ) {
					xhr.overrideMimeType( options.mimeType );
				}

				// X-Requested-With header
				// For cross-domain requests, seeing as conditions for a preflight are
				// akin to a jigsaw puzzle, we simply never set it to be sure.
				// (it can always be set on a per-request basis or even using ajaxSetup)
				// For same-domain requests, won't change header if already provided.
				if ( !options.crossDomain && !headers[ "X-Requested-With" ] ) {
					headers[ "X-Requested-With" ] = "XMLHttpRequest";
				}

				// Set headers
				for ( i in headers ) {
					xhr.setRequestHeader( i, headers[ i ] );
				}

				// Callback
				callback = function( type ) {
					return function() {
						if ( callback ) {
							callback = errorCallback = xhr.onload =
								xhr.onerror = xhr.onabort = xhr.ontimeout =
									xhr.onreadystatechange = null;

							if ( type === "abort" ) {
								xhr.abort();
							} else if ( type === "error" ) {

								// Support: IE <=9 only
								// On a manual native abort, IE9 throws
								// errors on any property access that is not readyState
								if ( typeof xhr.status !== "number" ) {
									complete( 0, "error" );
								} else {
									complete(

										// File: protocol always yields status 0; see trac-8605, trac-14207
										xhr.status,
										xhr.statusText
									);
								}
							} else {
								complete(
									xhrSuccessStatus[ xhr.status ] || xhr.status,
									xhr.statusText,

									// Support: IE <=9 only
									// IE9 has no XHR2 but throws on binary (trac-11426)
									// For XHR2 non-text, let the caller handle it (gh-2498)
									( xhr.responseType || "text" ) !== "text"  ||
									typeof xhr.responseText !== "string" ?
										{ binary: xhr.response } :
										{ text: xhr.responseText },
									xhr.getAllResponseHeaders()
								);
							}
						}
					};
				};

				// Listen to events
				xhr.onload = callback();
				errorCallback = xhr.onerror = xhr.ontimeout = callback( "error" );

				// Support: IE 9 only
				// Use onreadystatechange to replace onabort
				// to handle uncaught aborts
				if ( xhr.onabort !== undefined ) {
					xhr.onabort = errorCallback;
				} else {
					xhr.onreadystatechange = function() {

						// Check readyState before timeout as it changes
						if ( xhr.readyState === 4 ) {

							// Allow onerror to be called first,
							// but that will not handle a native abort
							// Also, save errorCallback to a variable
							// as xhr.onerror cannot be accessed
							window.setTimeout( function() {
								if ( callback ) {
									errorCallback();
								}
							} );
						}
					};
				}

				// Create the abort callback
				callback = callback( "abort" );

				try {

					// Do send the request (this may raise an exception)
					xhr.send( options.hasContent && options.data || null );
				} catch ( e ) {

					// trac-14683: Only rethrow if this hasn't been notified as an error yet
					if ( callback ) {
						throw e;
					}
				}
			},

			abort: function() {
				if ( callback ) {
					callback();
				}
			}
		};
	}
} );




// Prevent auto-execution of scripts when no explicit dataType was provided (See gh-2432)
jQuery.ajaxPrefilter( function( s ) {
	if ( s.crossDomain ) {
		s.contents.script = false;
	}
} );

// Install script dataType
jQuery.ajaxSetup( {
	accepts: {
		script: "text/javascript, application/javascript, " +
			"application/ecmascript, application/x-ecmascript"
	},
	contents: {
		script: /\b(?:java|ecma)script\b/
	},
	converters: {
		"text script": function( text ) {
			jQuery.globalEval( text );
			return text;
		}
	}
} );

// Handle cache's special case and crossDomain
jQuery.ajaxPrefilter( "script", function( s ) {
	if ( s.cache === undefined ) {
		s.cache = false;
	}
	if ( s.crossDomain ) {
		s.type = "GET";
	}
} );

// Bind script tag hack transport
jQuery.ajaxTransport( "script", function( s ) {

	// This transport only deals with cross domain or forced-by-attrs requests
	if ( s.crossDomain || s.scriptAttrs ) {
		var script, callback;
		return {
			send: function( _, complete ) {
				script = jQuery( "<script>" )
					.attr( s.scriptAttrs || {} )
					.prop( { charset: s.scriptCharset, src: s.url } )
					.on( "load error", callback = function( evt ) {
						script.remove();
						callback = null;
						if ( evt ) {
							complete( evt.type === "error" ? 404 : 200, evt.type );
						}
					} );

				// Use native DOM manipulation to avoid our domManip AJAX trickery
				document.head.appendChild( script[ 0 ] );
			},
			abort: function() {
				if ( callback ) {
					callback();
				}
			}
		};
	}
} );




var oldCallbacks = [],
	rjsonp = /(=)\?(?=&|$)|\?\?/;

// Default jsonp settings
jQuery.ajaxSetup( {
	jsonp: "callback",
	jsonpCallback: function() {
		var callback = oldCallbacks.pop() || ( jQuery.expando + "_" + ( nonce.guid++ ) );
		this[ callback ] = true;
		return callback;
	}
} );

// Detect, normalize options and install callbacks for jsonp requests
jQuery.ajaxPrefilter( "json jsonp", function( s, originalSettings, jqXHR ) {

	var callbackName, overwritten, responseContainer,
		jsonProp = s.jsonp !== false && ( rjsonp.test( s.url ) ?
			"url" :
			typeof s.data === "string" &&
				( s.contentType || "" )
					.indexOf( "application/x-www-form-urlencoded" ) === 0 &&
				rjsonp.test( s.data ) && "data"
		);

	// Handle iff the expected data type is "jsonp" or we have a parameter to set
	if ( jsonProp || s.dataTypes[ 0 ] === "jsonp" ) {

		// Get callback name, remembering preexisting value associated with it
		callbackName = s.jsonpCallback = isFunction( s.jsonpCallback ) ?
			s.jsonpCallback() :
			s.jsonpCallback;

		// Insert callback into url or form data
		if ( jsonProp ) {
			s[ jsonProp ] = s[ jsonProp ].replace( rjsonp, "$1" + callbackName );
		} else if ( s.jsonp !== false ) {
			s.url += ( rquery.test( s.url ) ? "&" : "?" ) + s.jsonp + "=" + callbackName;
		}

		// Use data converter to retrieve json after script execution
		s.converters[ "script json" ] = function() {
			if ( !responseContainer ) {
				jQuery.error( callbackName + " was not called" );
			}
			return responseContainer[ 0 ];
		};

		// Force json dataType
		s.dataTypes[ 0 ] = "json";

		// Install callback
		overwritten = window[ callbackName ];
		window[ callbackName ] = function() {
			responseContainer = arguments;
		};

		// Clean-up function (fires after converters)
		jqXHR.always( function() {

			// If previous value didn't exist - remove it
			if ( overwritten === undefined ) {
				jQuery( window ).removeProp( callbackName );

			// Otherwise restore preexisting value
			} else {
				window[ callbackName ] = overwritten;
			}

			// Save back as free
			if ( s[ callbackName ] ) {

				// Make sure that re-using the options doesn't screw things around
				s.jsonpCallback = originalSettings.jsonpCallback;

				// Save the callback name for future use
				oldCallbacks.push( callbackName );
			}

			// Call if it was a function and we have a response
			if ( responseContainer && isFunction( overwritten ) ) {
				overwritten( responseContainer[ 0 ] );
			}

			responseContainer = overwritten = undefined;
		} );

		// Delegate to script
		return "script";
	}
} );




// Support: Safari 8 only
// In Safari 8 documents created via document.implementation.createHTMLDocument
// collapse sibling forms: the second one becomes a child of the first one.
// Because of that, this security measure has to be disabled in Safari 8.
// https://bugs.webkit.org/show_bug.cgi?id=137337
support.createHTMLDocument = ( function() {
	var body = document.implementation.createHTMLDocument( "" ).body;
	body.innerHTML = "<form></form><form></form>";
	return body.childNodes.length === 2;
} )();


// Argument "data" should be string of html
// context (optional): If specified, the fragment will be created in this context,
// defaults to document
// keepScripts (optional): If true, will include scripts passed in the html string
jQuery.parseHTML = function( data, context, keepScripts ) {
	if ( typeof data !== "string" ) {
		return [];
	}
	if ( typeof context === "boolean" ) {
		keepScripts = context;
		context = false;
	}

	var base, parsed, scripts;

	if ( !context ) {

		// Stop scripts or inline event handlers from being executed immediately
		// by using document.implementation
		if ( support.createHTMLDocument ) {
			context = document.implementation.createHTMLDocument( "" );

			// Set the base href for the created document
			// so any parsed elements with URLs
			// are based on the document's URL (gh-2965)
			base = context.createElement( "base" );
			base.href = document.location.href;
			context.head.appendChild( base );
		} else {
			context = document;
		}
	}

	parsed = rsingleTag.exec( data );
	scripts = !keepScripts && [];

	// Single tag
	if ( parsed ) {
		return [ context.createElement( parsed[ 1 ] ) ];
	}

	parsed = buildFragment( [ data ], context, scripts );

	if ( scripts && scripts.length ) {
		jQuery( scripts ).remove();
	}

	return jQuery.merge( [], parsed.childNodes );
};


/**
 * Load a url into a page
 */
jQuery.fn.load = function( url, params, callback ) {
	var selector, type, response,
		self = this,
		off = url.indexOf( " " );

	if ( off > -1 ) {
		selector = stripAndCollapse( url.slice( off ) );
		url = url.slice( 0, off );
	}

	// If it's a function
	if ( isFunction( params ) ) {

		// We assume that it's the callback
		callback = params;
		params = undefined;

	// Otherwise, build a param string
	} else if ( params && typeof params === "object" ) {
		type = "POST";
	}

	// If we have elements to modify, make the request
	if ( self.length > 0 ) {
		jQuery.ajax( {
			url: url,

			// If "type" variable is undefined, then "GET" method will be used.
			// Make value of this field explicit since
			// user can override it through ajaxSetup method
			type: type || "GET",
			dataType: "html",
			data: params
		} ).done( function( responseText ) {

			// Save response for use in complete callback
			response = arguments;

			self.html( selector ?

				// If a selector was specified, locate the right elements in a dummy div
				// Exclude scripts to avoid IE 'Permission Denied' errors
				jQuery( "<div>" ).append( jQuery.parseHTML( responseText ) ).find( selector ) :

				// Otherwise use the full result
				responseText );

		// If the request succeeds, this function gets "data", "status", "jqXHR"
		// but they are ignored because response was set above.
		// If it fails, this function gets "jqXHR", "status", "error"
		} ).always( callback && function( jqXHR, status ) {
			self.each( function() {
				callback.apply( this, response || [ jqXHR.responseText, status, jqXHR ] );
			} );
		} );
	}

	return this;
};




jQuery.expr.pseudos.animated = function( elem ) {
	return jQuery.grep( jQuery.timers, function( fn ) {
		return elem === fn.elem;
	} ).length;
};




jQuery.offset = {
	setOffset: function( elem, options, i ) {
		var curPosition, curLeft, curCSSTop, curTop, curOffset, curCSSLeft, calculatePosition,
			position = jQuery.css( elem, "position" ),
			curElem = jQuery( elem ),
			props = {};

		// Set position first, in-case top/left are set even on static elem
		if ( position === "static" ) {
			elem.style.position = "relative";
		}

		curOffset = curElem.offset();
		curCSSTop = jQuery.css( elem, "top" );
		curCSSLeft = jQuery.css( elem, "left" );
		calculatePosition = ( position === "absolute" || position === "fixed" ) &&
			( curCSSTop + curCSSLeft ).indexOf( "auto" ) > -1;

		// Need to be able to calculate position if either
		// top or left is auto and position is either absolute or fixed
		if ( calculatePosition ) {
			curPosition = curElem.position();
			curTop = curPosition.top;
			curLeft = curPosition.left;

		} else {
			curTop = parseFloat( curCSSTop ) || 0;
			curLeft = parseFloat( curCSSLeft ) || 0;
		}

		if ( isFunction( options ) ) {

			// Use jQuery.extend here to allow modification of coordinates argument (gh-1848)
			options = options.call( elem, i, jQuery.extend( {}, curOffset ) );
		}

		if ( options.top != null ) {
			props.top = ( options.top - curOffset.top ) + curTop;
		}
		if ( options.left != null ) {
			props.left = ( options.left - curOffset.left ) + curLeft;
		}

		if ( "using" in options ) {
			options.using.call( elem, props );

		} else {
			curElem.css( props );
		}
	}
};

jQuery.fn.extend( {

	// offset() relates an element's border box to the document origin
	offset: function( options ) {

		// Preserve chaining for setter
		if ( arguments.length ) {
			return options === undefined ?
				this :
				this.each( function( i ) {
					jQuery.offset.setOffset( this, options, i );
				} );
		}

		var rect, win,
			elem = this[ 0 ];

		if ( !elem ) {
			return;
		}

		// Return zeros for disconnected and hidden (display: none) elements (gh-2310)
		// Support: IE <=11 only
		// Running getBoundingClientRect on a
		// disconnected node in IE throws an error
		if ( !elem.getClientRects().length ) {
			return { top: 0, left: 0 };
		}

		// Get document-relative position by adding viewport scroll to viewport-relative gBCR
		rect = elem.getBoundingClientRect();
		win = elem.ownerDocument.defaultView;
		return {
			top: rect.top + win.pageYOffset,
			left: rect.left + win.pageXOffset
		};
	},

	// position() relates an element's margin box to its offset parent's padding box
	// This corresponds to the behavior of CSS absolute positioning
	position: function() {
		if ( !this[ 0 ] ) {
			return;
		}

		var offsetParent, offset, doc,
			elem = this[ 0 ],
			parentOffset = { top: 0, left: 0 };

		// position:fixed elements are offset from the viewport, which itself always has zero offset
		if ( jQuery.css( elem, "position" ) === "fixed" ) {

			// Assume position:fixed implies availability of getBoundingClientRect
			offset = elem.getBoundingClientRect();

		} else {
			offset = this.offset();

			// Account for the *real* offset parent, which can be the document or its root element
			// when a statically positioned element is identified
			doc = elem.ownerDocument;
			offsetParent = elem.offsetParent || doc.documentElement;
			while ( offsetParent &&
				( offsetParent === doc.body || offsetParent === doc.documentElement ) &&
				jQuery.css( offsetParent, "position" ) === "static" ) {

				offsetParent = offsetParent.parentNode;
			}
			if ( offsetParent && offsetParent !== elem && offsetParent.nodeType === 1 ) {

				// Incorporate borders into its offset, since they are outside its content origin
				parentOffset = jQuery( offsetParent ).offset();
				parentOffset.top += jQuery.css( offsetParent, "borderTopWidth", true );
				parentOffset.left += jQuery.css( offsetParent, "borderLeftWidth", true );
			}
		}

		// Subtract parent offsets and element margins
		return {
			top: offset.top - parentOffset.top - jQuery.css( elem, "marginTop", true ),
			left: offset.left - parentOffset.left - jQuery.css( elem, "marginLeft", true )
		};
	},

	// This method will return documentElement in the following cases:
	// 1) For the element inside the iframe without offsetParent, this method will return
	//    documentElement of the parent window
	// 2) For the hidden or detached element
	// 3) For body or html element, i.e. in case of the html node - it will return itself
	//
	// but those exceptions were never presented as a real life use-cases
	// and might be considered as more preferable results.
	//
	// This logic, however, is not guaranteed and can change at any point in the future
	offsetParent: function() {
		return this.map( function() {
			var offsetParent = this.offsetParent;

			while ( offsetParent && jQuery.css( offsetParent, "position" ) === "static" ) {
				offsetParent = offsetParent.offsetParent;
			}

			return offsetParent || documentElement;
		} );
	}
} );

// Create scrollLeft and scrollTop methods
jQuery.each( { scrollLeft: "pageXOffset", scrollTop: "pageYOffset" }, function( method, prop ) {
	var top = "pageYOffset" === prop;

	jQuery.fn[ method ] = function( val ) {
		return access( this, function( elem, method, val ) {

			// Coalesce documents and windows
			var win;
			if ( isWindow( elem ) ) {
				win = elem;
			} else if ( elem.nodeType === 9 ) {
				win = elem.defaultView;
			}

			if ( val === undefined ) {
				return win ? win[ prop ] : elem[ method ];
			}

			if ( win ) {
				win.scrollTo(
					!top ? val : win.pageXOffset,
					top ? val : win.pageYOffset
				);

			} else {
				elem[ method ] = val;
			}
		}, method, val, arguments.length );
	};
} );

// Support: Safari <=7 - 9.1, Chrome <=37 - 49
// Add the top/left cssHooks using jQuery.fn.position
// Webkit bug: https://bugs.webkit.org/show_bug.cgi?id=29084
// Blink bug: https://bugs.chromium.org/p/chromium/issues/detail?id=589347
// getComputedStyle returns percent when specified for top/left/bottom/right;
// rather than make the css module depend on the offset module, just check for it here
jQuery.each( [ "top", "left" ], function( _i, prop ) {
	jQuery.cssHooks[ prop ] = addGetHookIf( support.pixelPosition,
		function( elem, computed ) {
			if ( computed ) {
				computed = curCSS( elem, prop );

				// If curCSS returns percentage, fallback to offset
				return rnumnonpx.test( computed ) ?
					jQuery( elem ).position()[ prop ] + "px" :
					computed;
			}
		}
	);
} );


// Create innerHeight, innerWidth, height, width, outerHeight and outerWidth methods
jQuery.each( { Height: "height", Width: "width" }, function( name, type ) {
	jQuery.each( {
		padding: "inner" + name,
		content: type,
		"": "outer" + name
	}, function( defaultExtra, funcName ) {

		// Margin is only for outerHeight, outerWidth
		jQuery.fn[ funcName ] = function( margin, value ) {
			var chainable = arguments.length && ( defaultExtra || typeof margin !== "boolean" ),
				extra = defaultExtra || ( margin === true || value === true ? "margin" : "border" );

			return access( this, function( elem, type, value ) {
				var doc;

				if ( isWindow( elem ) ) {

					// $( window ).outerWidth/Height return w/h including scrollbars (gh-1729)
					return funcName.indexOf( "outer" ) === 0 ?
						elem[ "inner" + name ] :
						elem.document.documentElement[ "client" + name ];
				}

				// Get document width or height
				if ( elem.nodeType === 9 ) {
					doc = elem.documentElement;

					// Either scroll[Width/Height] or offset[Width/Height] or client[Width/Height],
					// whichever is greatest
					return Math.max(
						elem.body[ "scroll" + name ], doc[ "scroll" + name ],
						elem.body[ "offset" + name ], doc[ "offset" + name ],
						doc[ "client" + name ]
					);
				}

				return value === undefined ?

					// Get width or height on the element, requesting but not forcing parseFloat
					jQuery.css( elem, type, extra ) :

					// Set width or height on the element
					jQuery.style( elem, type, value, extra );
			}, type, chainable ? margin : undefined, chainable );
		};
	} );
} );


jQuery.each( [
	"ajaxStart",
	"ajaxStop",
	"ajaxComplete",
	"ajaxError",
	"ajaxSuccess",
	"ajaxSend"
], function( _i, type ) {
	jQuery.fn[ type ] = function( fn ) {
		return this.on( type, fn );
	};
} );




jQuery.fn.extend( {

	bind: function( types, data, fn ) {
		return this.on( types, null, data, fn );
	},
	unbind: function( types, fn ) {
		return this.off( types, null, fn );
	},

	delegate: function( selector, types, data, fn ) {
		return this.on( types, selector, data, fn );
	},
	undelegate: function( selector, types, fn ) {

		// ( namespace ) or ( selector, types [, fn] )
		return arguments.length === 1 ?
			this.off( selector, "**" ) :
			this.off( types, selector || "**", fn );
	},

	hover: function( fnOver, fnOut ) {
		return this.mouseenter( fnOver ).mouseleave( fnOut || fnOver );
	}
} );

jQuery.each(
	( "blur focus focusin focusout resize scroll click dblclick " +
	"mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " +
	"change select submit keydown keypress keyup contextmenu" ).split( " " ),
	function( _i, name ) {

		// Handle event binding
		jQuery.fn[ name ] = function( data, fn ) {
			return arguments.length > 0 ?
				this.on( name, null, data, fn ) :
				this.trigger( name );
		};
	}
);




// Support: Android <=4.0 only
// Make sure we trim BOM and NBSP
// Require that the "whitespace run" starts from a non-whitespace
// to avoid O(N^2) behavior when the engine would try matching "\s+$" at each space position.
var rtrim = /^[\s\uFEFF\xA0]+|([^\s\uFEFF\xA0])[\s\uFEFF\xA0]+$/g;

// Bind a function to a context, optionally partially applying any
// arguments.
// jQuery.proxy is deprecated to promote standards (specifically Function#bind)
// However, it is not slated for removal any time soon
jQuery.proxy = function( fn, context ) {
	var tmp, args, proxy;

	if ( typeof context === "string" ) {
		tmp = fn[ context ];
		context = fn;
		fn = tmp;
	}

	// Quick check to determine if target is callable, in the spec
	// this throws a TypeError, but we will just return undefined.
	if ( !isFunction( fn ) ) {
		return undefined;
	}

	// Simulated bind
	args = slice.call( arguments, 2 );
	proxy = function() {
		return fn.apply( context || this, args.concat( slice.call( arguments ) ) );
	};

	// Set the guid of unique handler to the same of original handler, so it can be removed
	proxy.guid = fn.guid = fn.guid || jQuery.guid++;

	return proxy;
};

jQuery.holdReady = function( hold ) {
	if ( hold ) {
		jQuery.readyWait++;
	} else {
		jQuery.ready( true );
	}
};
jQuery.isArray = Array.isArray;
jQuery.parseJSON = JSON.parse;
jQuery.nodeName = nodeName;
jQuery.isFunction = isFunction;
jQuery.isWindow = isWindow;
jQuery.camelCase = camelCase;
jQuery.type = toType;

jQuery.now = Date.now;

jQuery.isNumeric = function( obj ) {

	// As of jQuery 3.0, isNumeric is limited to
	// strings and numbers (primitives or objects)
	// that can be coerced to finite numbers (gh-2662)
	var type = jQuery.type( obj );
	return ( type === "number" || type === "string" ) &&

		// parseFloat NaNs numeric-cast false positives ("")
		// ...but misinterprets leading-number strings, particularly hex literals ("0x...")
		// subtraction forces infinities to NaN
		!isNaN( obj - parseFloat( obj ) );
};

jQuery.trim = function( text ) {
	return text == null ?
		"" :
		( text + "" ).replace( rtrim, "$1" );
};



// Register as a named AMD module, since jQuery can be concatenated with other
// files that may use define, but not via a proper concatenation script that
// understands anonymous AMD modules. A named AMD is safest and most robust
// way to register. Lowercase jquery is used because AMD module names are
// derived from file names, and jQuery is normally delivered in a lowercase
// file name. Do this after creating the global so that if an AMD module wants
// to call noConflict to hide this version of jQuery, it will work.

// Note that for maximum portability, libraries that are not jQuery should
// declare themselves as anonymous modules, and avoid setting a global if an
// AMD loader is present. jQuery is a special case. For more information, see
// https://github.com/jrburke/requirejs/wiki/Updating-existing-libraries#wiki-anon

if ( typeof define === "function" && define.amd ) {
	define( "jquery", [], function() {
		return jQuery;
	} );
}




var

	// Map over jQuery in case of overwrite
	_jQuery = window.jQuery,

	// Map over the $ in case of overwrite
	_$ = window.$;

jQuery.noConflict = function( deep ) {
	if ( window.$ === jQuery ) {
		window.$ = _$;
	}

	if ( deep && window.jQuery === jQuery ) {
		window.jQuery = _jQuery;
	}

	return jQuery;
};

// Expose jQuery and $ identifiers, even in AMD
// (trac-7102#comment:10, https://github.com/jquery/jquery/pull/557)
// and CommonJS for browser emulators (trac-13566)
if ( typeof noGlobal === "undefined" ) {
	window.jQuery = window.$ = jQuery;
}




return jQuery;
} );

/*! js-cookie v3.0.1 | MIT */
;
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = global || self, (function () {
    var current = global.Cookies;
    var exports = global.Cookies = factory();
    exports.noConflict = function () { global.Cookies = current; return exports; };
  }()));
}(this, (function () { 'use strict';

  /* eslint-disable no-var */
  function assign (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        target[key] = source[key];
      }
    }
    return target
  }
  /* eslint-enable no-var */

  /* eslint-disable no-var */
  var defaultConverter = {
    read: function (value) {
      if (value[0] === '"') {
        value = value.slice(1, -1);
      }
      return value.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent)
    },
    write: function (value) {
      return encodeURIComponent(value).replace(
        /%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,
        decodeURIComponent
      )
    }
  };
  /* eslint-enable no-var */

  /* eslint-disable no-var */

  function init (converter, defaultAttributes) {
    function set (key, value, attributes) {
      if (typeof document === 'undefined') {
        return
      }

      attributes = assign({}, defaultAttributes, attributes);

      if (typeof attributes.expires === 'number') {
        attributes.expires = new Date(Date.now() + attributes.expires * 864e5);
      }
      if (attributes.expires) {
        attributes.expires = attributes.expires.toUTCString();
      }

      key = encodeURIComponent(key)
        .replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent)
        .replace(/[()]/g, escape);

      var stringifiedAttributes = '';
      for (var attributeName in attributes) {
        if (!attributes[attributeName]) {
          continue
        }

        stringifiedAttributes += '; ' + attributeName;

        if (attributes[attributeName] === true) {
          continue
        }

        // Considers RFC 6265 section 5.2:
        // ...
        // 3.  If the remaining unparsed-attributes contains a %x3B (";")
        //     character:
        // Consume the characters of the unparsed-attributes up to,
        // not including, the first %x3B (";") character.
        // ...
        stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
      }

      return (document.cookie =
        key + '=' + converter.write(value, key) + stringifiedAttributes)
    }

    function get (key) {
      if (typeof document === 'undefined' || (arguments.length && !key)) {
        return
      }

      // To prevent the for loop in the first place assign an empty array
      // in case there are no cookies at all.
      var cookies = document.cookie ? document.cookie.split('; ') : [];
      var jar = {};
      for (var i = 0; i < cookies.length; i++) {
        var parts = cookies[i].split('=');
        var value = parts.slice(1).join('=');

        try {
          var foundKey = decodeURIComponent(parts[0]);
          jar[foundKey] = converter.read(value, foundKey);

          if (key === foundKey) {
            break
          }
        } catch (e) {}
      }

      return key ? jar[key] : jar
    }

    return Object.create(
      {
        set: set,
        get: get,
        remove: function (key, attributes) {
          set(
            key,
            '',
            assign({}, attributes, {
              expires: -1
            })
          );
        },
        withAttributes: function (attributes) {
          return init(this.converter, assign({}, this.attributes, attributes))
        },
        withConverter: function (converter) {
          return init(assign({}, this.converter, converter), this.attributes)
        }
      },
      {
        attributes: { value: Object.freeze(defaultAttributes) },
        converter: { value: Object.freeze(converter) }
      }
    )
  }

  var api = init(defaultConverter, { path: '/' });
  /* eslint-enable no-var */

  return api;

})));

/**

  This file should be removed 
  on Bootstrap 5+

**/
// -- -- --
// Polyfill: Array forEach
// Production steps of ECMA-262, Edition 5, 15.4.4.18
// Reference: http://es5.github.io/#x15.4.4.18
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
if ( !Array.prototype['forEach'] ) {
	Array.prototype.forEach=function(r,o){if(null==this)throw new TypeError("Array.prototype.forEach called on null or undefined");var n,t,e=Object(this),f=e.length>>>0;if("function"!=typeof r)throw new TypeError(r+" is not a function");for(arguments.length>1&&(n=o),t=0;t<f;){var i;t in e&&(i=e[t],r.call(n,i,t,e)),t++}};
}
// [IE 9+] Polyfill: forEach
// https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach
// document.querySelectorAll('.selector').forEach(function(e) {...});
// array.forEach(function(e) {...});
if ( window.NodeList && !NodeList.prototype.forEach ) {
  NodeList.prototype.forEach = Array.prototype.forEach;
}
/**
 *
 *  [SOW] Stepofweb Controller
 *
 *  @author Dorin Grigoras
 *          www.stepofweb.com
 *
 *  @version 1.1.0


    @globals

        $.SOW.globals.direction                         'ltr|rtl'
        $.SOW.globals.width                             actual width    (updated on resize)
        $.SOW.globals.height                            actual height   (updated on resize)
        $.SOW.globals.is_mobile                         true|false
        $.SOW.globals.is_modern_browser                 true if modern
        $.SOW.globals.is_admin                          true|false      (admin layout)
        $.SOW.globals.breakpoints.[sm|ms|lg|xl]         bootstrap default breakpoints
        $.SOW.globals.elBody                            body element
        $.SOW.globals.elHeader                          header element
        $.SOW.globals.elAside                           main sidebar element

    @functions  
        $.SOW.reinit('#container')                      reinit plugins for a specific ajax container; see also:

 *
 **/
;(function ($) {
  'use strict';

  /**
   *
   *  @vars
   *
   *
   **/
   var _v = '1.1.0';


  $.SOW = {


    /**
     *
     *  @init
     *
     *
     **/
    init: function () {

      // <script async> NOT working. Keep jQuery!
      // document.addEventListener('DOMContentLoaded', function() {

      $(document).ready(function() { 

        /* 

            Check if debug is enabled
            Should be disabled on production!

        */
        if($.SOW.config.sow__debug_enable === true) {

          $.SOW.helper.consoleLog('++++++++++++++++++++++++++++++');
          $.SOW.helper.consoleLog('+ SOW Controller : ' + _v, 'color: #ff0000; font-size: 13px;');
          $.SOW.helper.consoleLog('++++++++++++++++++++++++++++++');

        }

        // on load
        $.SOW.globals.js_location   = $.SOW.helper.jsLocation();
        $.SOW.globals.css_location  = $.SOW.helper.cssLocation();
        $.SOW.onresize();



        /*
        
            1.  Check for bootstrap
                ::  Part of bundle file! 
                    vendor_bundle.min.js

        */
        // if( typeof $().emulateTransitionEnd === 'function' ) { // bs4
        if( $.fn.modal ) {
          $.SOW.reinit(); /* first init ; ajax callable */
          return;
        }



        /*
        
            1.  Bundle not loaded
                ::  Load it! And init!
                    vendor_bundle.min.js

        */
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        //$.SOW.helper.loadScript([$.SOW.globals.js_location + 'vendor_bundle.min.js'], false, true).done(function() {
        //  $.SOW.helper.consoleLog('Vendor Bunde: Dynamically loaded!');
        //  $.SOW.reinit(); /* first init ; ajax callable */
        //});
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

      });

    },



    /**
     *
     *  @globals
     *  SOW Config
     *
     **/
    globals: {

      direction           : $('body').css('direction'),   /* rtl | ltr */
      width               : $(window).width(),            /* window width, updated on resize */
      height              : $(window).height(),           /* window height, updated on resize */
      is_modern_browser   : ('querySelector' in document && 'localStorage' in window && 'addEventListener' in window),    /* `true` if modern */
      is_mobile           : ($(window).width() < 993)             ? true : false,
      is_admin            : $('body').hasClass('layout-admin')    ? true : false,
      ajax_container      : 'body',                       /* DO NOT USE THIS IN YOUR SCRIPT, IS EXCLUSIVELY USED BY REINIT() FUNCTION */
      page_first_load     : true,                         /* set by reinit() to false after first load - used by ajax */
      js_location         : '',                           /* javascripts location, used to dinamicaly load js scripts */
      css_location        : '',                           /* javascripts location, used to dinamicaly load css */
      cookie_secure       : 'SameSite=None; secure',      /* New Google Secure Cookie */

      /* bootstrap breakpoints */
      breakpoints     : {
          'sm': 576,
          'md': 768,
          'lg': 992,
          'xl': 1200
      },

      /* 
          Most used only!
          Cache once : Use everywhere 
      */
      elBody          : $('body'),
      elHeader        : ($('#header').length > 0)     ? $('#header')      : null,
      elAside         : ($('#aside-main').length > 0) ? $('#aside-main')  : null,

    },



    /**
     *
     *  @core
     *  SOW Core Plugins
     *
     **/
    core: {},



    /**
     *
     *  @vendor
     *  Vendor Plugins [separated by SOW]
     *
     **/
    vendor: {},



    /**
     *
     *  @helper
     *  SOW Helpers
     *
     **/
    helper: {},



    /**
     *
     *  @custom
     *  My Custom [optional]
     *
     **/
    custom: {},



    /**
     *
     *  @resize
     *  Window Resize
     *
     **/
    onresize: function() {

      // On Resize
      jQuery(window).resize(function() {

          if(window.afterResizeApp)
              clearTimeout(window.afterResizeApp);

          window.afterResizeApp = setTimeout(function() {

            /** Window Width **/
            $.SOW.globals.width     = jQuery(window).width();

            /** Window Height **/
            $.SOW.globals.height    = jQuery(window).height();

            /** Is Mobile **/
            $.SOW.globals.is_mobile = ($(window).width() < 993) ? true : false;


          }, 150);

      });

    },



    /**
     *
     *  @reinit
     *  Ajax Callable
     *
     **/
    reinit: function(ajax_container) {

      /*
          For each Ajax call, we temporarily set the ajax container as global
          After reinit, we reset back the ajax container as 'body'
      */
      $.SOW.globals.ajax_container = $.SOW.helper.check_var(ajax_container) || 'body';


      /** Bootstrap Toasts **/ 
     // $($.SOW.globals.ajax_container + ' .toast').toast('show');


      /** Bootstrap Tooltip **/ 
      //$($.SOW.globals.ajax_container + " [data-bs-toggle=tooltip]," + $.SOW.globals.ajax_container + " [data-tooltip]").tooltip('dispose').tooltip({
      //  container: ($.SOW.globals.ajax_container == 'body') ? 'html' : $.SOW.globals.ajax_container /* fixing wired positioning! */
      //}).on('focus', function () {  $(this).blur() });


      /** Bootstrap Popover **/
      //let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      //let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      //  return new bootstrap.Popover(popoverTriggerEl)
      //});


      /** Bootstrap Carousel **/
      //$($.SOW.globals.ajax_container + ' .carousel').carousel('dispose').carousel({
      //  direction:  ($.SOW.globals.direction == 'ltr') ? 'right' : 'left'
      //});

      /** Bootstrap Scrollspy **/
      // $('[data-bs-spy="scroll"]').each(function () {
      //   $(this).scrollspy('refresh');
      // });

      // let dataSpyList = [].slice.call(document.querySelectorAll('[data-bs-spy="scroll"]'));
      // dataSpyList.forEach(function (dataSpyEl) {
      //   bootstrap.ScrollSpy.getInstance(dataSpyEl).refresh()
      // });


      /*

          Autoinit plugins
          Specified in Config

      */
      // for (var index = 0; index < $.SOW.config.autoinit.length; ++index) {
      for (var index in $.SOW.config.autoinit) {

        // Not first page load, skip if plugin do not allow reinit by ajax
        if($.SOW.globals.page_first_load === false && $.SOW.config.autoinit[index][3] === false)
          continue;

        $.SOW.helper.executeFunctionByName(
            $.SOW.config.autoinit[index][0],    // script
            window, 
            $.SOW.config.autoinit[index][1],    // selector
            $.SOW.config.autoinit[index][2]     // config
        );   

      }


      /*
          
          Reserved for emergencies!
          Called for each ajax container!

          global_callback = function(ajax_container) {
              ...
          }

      */
      if(typeof global_callback === 'function')
        $.SOW.helper.executeFunctionByName('global_callback', window, $.SOW.globals.ajax_container);


      /*
          Page classic preloader : first load only!
      */
      if($.SOW.globals.page_first_load === true) {

        jQuery('#page_preload').fadeOut(1000, function() {
          jQuery(this).remove();
        });

      }

      // First page load finished!
      // Any future reinit() calls are Ajax!
      $.SOW.globals.page_first_load   = false;
      $.SOW.globals.ajax_container    = 'body'; // reset

    },


  };



  /**
   *
   *  Init this
   *
   *
   **/
  $.SOW.init();

})(jQuery);
/**
 *
 *  [SOW] Config
 *
 **/
;(function ($) {
  'use strict';

  $.SOW.config = {

    /*
        
        +++++++++++++++++++++++++++++++++
        OVERWRITEN BY GULP : LEAVE IT FALSE!
        Browser console global debuger!
        +++++++++++++++++++++++++++++++++

    */
    sow__debug_enable                               : false,



    /* 

        SOW : VARIOUS
        -----------------------------------------------------

    */
    /* Icons */
    sow__icon_loading                               : 'fi fi-circle-spin fi-spin',      // ajax loading indicator
    sow__icon_check                                 : 'fi fi-check',                    // 'success' messages or other places
    sow__icon_close                                 : 'fi fi-close',                    // 'failed'  messages or other places

  };

})(jQuery);
/**
 *
 *  [SOW] Helper
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *
 *  @Dependency     -
 *

    *   @check_var
        $.SOW.helper.check_var(variable);
    *
        @check_var
        $.SOW.helper.is_numeric(str);
    *
        @loadScript
        $.SOW.helper.loadScript(script_arr, async[true|false], cache[true|false]).done(function() {
            // all scripts loaded... do something
            // * async = false by default (scripts are loaded in order)
            // * cache = true by default
        });


     *
        @loadCSS
        $.SOW.helper.loadCSS("/path/to/file.css", "append|prepend|remove");  "append" is default, if no option passed
     *
        @loadingSpinner
        $.SOW.helper.loadingSpinner('show|hide', "#mycontainer", 'overlay|icon');
     *
        @executeFunctionByName
        $.SOW.helper.executeFunctionByName("FunctionName", window, arguments);
     *
        @overlay
        $.SOW.helper.overlay('show|hide|toggle');
     *
        @randomStr
        $.SOW.helper.randomStr(8, ''|L|N);
     *
        @byte2size
        $.SOW.helper.byte2size(bytes, precision=2, int_only=false);
        $.SOW.helper.byte2kb(bytes);
     *
        @scrollAnimate
        $.SOW.helper.scrollAnimate(_el, _offset, _hash_change, _speed);
            _el             = element to scroll to. #top = page top
            _offset         = top offset (0 default)
            _hash_change    = update url hash on click
            _speed          = scroll speed (400 default)

        $.SOW.helper.scrollAnimate('#top', 0, false, 400);
     *
        @removeUrlHash
        $.SOW.helper.removeUrlHash('https://domain.com/url#hash');
     *
        @playSound
        $.SOW.helper.playSound('path/to/audio.mp3');
     *
        @time_from_ms
        $.SOW.helper.time_from_ms(miliseconds, 's|m|h|d|empty for all');
     *  
        @get_browser (unfinished, need improvement, do not use)
        $.SOW.helper.get_browser();
     *
        @params_parse
        var params = $.SOW.helper.params_parse('['param','value']['param2','value2']); // return: array
            
            var ajax_params_arr = $.SOW.helper.params_parse(ajax_params);
            for (var i = 0; i < ajax_params_arr.length; ++i) {
                formDataDel.append(ajax_params_arr[i][0], ajax_params_arr[i][1]);
            }
     *
      *//*  @currencyFormat*//*
        $.SOW.helper.currencyFormat(1000000); // output: 1,234,567.89

        // 1,234,567.89
        $.SOW.helper.currencyFormat(1000000, [
               2, ',', '.' // en
            // 2, '.', ',' // de
            // 2, ' ', ',' // fr
        ]);
     *
        @strhash    
        $.SOW.helper.strhash('string here'); // create a hash, md5 alternative
     *
        @jsonParse
        $.SOW.helper.jsonParse('object/string here');
     *
        @serializeArray
        $.SOW.helper.serializeArray(form);
     *
        @compareArray 
        Return: true|false
        $.SOW.helper.compareArray(array1, array2);
        $.SOW.helper.compareArray([2,3,1,4], [1,2,3,4]);
     *
         @videoEmbedFromUrl
         $.SOW.helper.videoEmbedFromUrl('https://www.youtube.com?v=jh8Hgd466', autoplay=1);
     *
        @consoleLog (output - only if debug is enabled!)
        $.SOW.helper.consoleLog('Lorem Ipsum', 'color: #ff0000;');
 *
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var obj             = {};               // used by loadScript


  $.SOW.helper = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {},



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *
     *
     **/
    init: function (selector, config) {/** no init required **/},




    /**
     *
     *  @__selector
     *  DO NOT! DO NOT CHANGE!
     *  $.SOW.helper.__selector(selector);
     *
     **/
    __selector: function(selector) {

      var selector        = $.SOW.helper.check_var(selector) || ''; /* '' is required if null */
      var selector_orig   = selector;
      var element         = (selector && $(selector).length > 0) ? $(selector) : $();

      /* add ajax container - required for binds */
      if($.SOW.globals.ajax_container != 'html' && $.SOW.globals.ajax_container != 'body' && $.SOW.globals.ajax_container != '') {

        if (selector.indexOf(',') > -1)
          var selector = selector.split(',').join(', ' + $.SOW.globals.ajax_container + ' ');

        selector = $.SOW.globals.ajax_container + ' ' + selector;

      }

      return [selector, element, selector_orig]; // selector_orig = without ajax container, in case is needed

    },






    /**
     *
     *  @check_var
     *
        $.SOW.helper.check_var(variable);
     *
     **/
    check_var: function(_var) {

      return  (typeof _var !== "undefined") ? _var : null;

    },







    /**
     *
     *  @is_numeric
     *
     *  Welcome to Javascript!
     *  Please, stay! Is nice in here!
     *
        $.SOW.helper.is_numeric(_var);
     *  1.2 = true  ;   1,2 = false
     **/
    is_numeric: function(_var) {

      if(typeof _var === 'number') return true;

      // at this point, we might have bool/object/function/etc
      else if(typeof _var !== 'string') return false;

      // -- --

      var _var = ''+_var.replace(/\s/g, '');
      if(_var === '') return false;

      // something like '1.'
      else if(_var.slice(-1) === '.') return false;  

      // -- --

      return !isNaN(parseFloat(_var)) && isFinite(_var);

    },







    /**
     *
     *  @loadScript
     *
        
        async false:
            loads scripts one-by-one using recursion (ordered)
            returns jQuery.Deferred

        async true:
            loads scripts asynchronized, if order is not needed!
            returns jQuery.when



            var script_arr = [
                'myscript1.js', 
                'myscript2.js', 
                'myscript3.js'
            ];

            $.SOW.helper.loadScript(script_arr, async[true|false], cache[true|false]).done(function() {
                // all scripts loaded... do something
                // * async = false by default (scripts are loaded in order)
                // * cache = true by default
            });

     *
     **/
    loadScript: function(script_arr, async, cache) {

      return (async === true) ? $.SOW.helper.__loadScriptAsync(script_arr, cache) : $.SOW.helper.__loadScriptOrdered(script_arr, cache);

    },

        /*

            Credits (Salman A : stackovweflow user)
                https://stackoverflow.com/a/33312665

        */
        __loadScriptOrdered: function(script_arr, cache) {

          var deferred = jQuery.Deferred();

          function __loadScript(i) {

              if (i < script_arr.length) {

                  jQuery.ajax({

                    url:        script_arr[i],
                    dataType:   "script",
                    cache:      (cache !== false) ? true : false,

                    success:    function() {

                      __loadScript(i + 1);

                    }

                  });

              } else {

                deferred.resolve();

              }

          }

          __loadScript(0);
          return deferred;

        },

        /*

            Credits (adeneo stackovweflow user)
                https://stackoverflow.com/a/11803418

        */
        __loadScriptAsync: function(script_arr, cache) {

          var _arr = $.map(script_arr, function(scr) {
            return (cache !== false) ? $.SOW.helper.getScriptCached( scr ) : $.getScript( scr );
          });

          _arr.push($.Deferred(function( deferred ) {
            $( deferred.resolve );
          }));

          return $.when.apply($, _arr);

        },
            getScriptCached: function(url, options) {

              // Allow user to set any option except for dataType, cache, and url
              options = $.extend( options || {}, {
                dataType: "script",
                cache: true,
                url: url
              });

              // Use $.ajax() since it is more flexible than $.getScript
              // Return the jqXHR object so we can chain callbacks
              return jQuery.ajax( options );

            },




    /**
     *
     *  @loadCSS
     *
        $.SOW.helper.loadCSS("/path/to/file.css", "append|prepend|remove");  
        "append" is default, if no option passed
     *
     **/
    loadCSS: function(cssFile, option) {

      /* 1. remove */
      if(option === 'remove')
        jQuery('head link[href="'+cssFile+'"]').remove();


      /* 2. prepend */
      else if(option === 'prepend') {
        if(jQuery('head link[href="'+cssFile+'"]').length > 0) return;
        jQuery('head').prepend('<link rel="stylesheet" href="'+cssFile+'">');
      }


      /* 3. append : default */
      else  {
        if(jQuery('head link[href="'+cssFile+'"]').length > 0) return;
        jQuery('head').append('<link rel="stylesheet" href="'+cssFile+'">');
      }


    },




    /**
     *
     *  @loadingSpinner
     *
        $.SOW.helper.loadingSpinner('show|hide', "#mycontainer", 'overlay|icon', 'overlay BG|icon text-*');

        [ Simple ]
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'icon');
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'overlay');

        [ Overlay ]
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'overlay', 'light');
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'overlay', 'dark');

        [ Overlay + Icon color ]
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'overlay', 'light:text-danger');
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'overlay', 'dark:text-danger');
        
        [ Icon color ]
        $.SOW.helper.loadingSpinner('show', "#mycontainer", 'icon', 'text-danger');

     *
     **/
    loadingSpinner: function(option, _container, _layout, _color) {

      var option          = (typeof option !== 'undefined')   ? option        : 'show';
      var _container      = (_container === null) ? $.SOW.globals.elBody      : _container;
      var _layout         = (typeof _layout !== 'undefined')  ? _layout       : 'icon';
      var _color          = (typeof _color !== 'undefined')   ? _color        : null;
      var _colorOverlay   = 'overlay-dark overlay-opacity-2';
      var _colorIcon      = 'text-muted';


      // Icon Color
      if( _layout == 'icon' && _color != '' )
        _colorIcon = _color;


      // Overlay Color + Icon Color
      if( _layout == 'overlay' && _color != '' ) {
        var s = _color.split(':');

        // Overlay
        if( typeof s[0] !== 'undefined' ) {
                 if( s[0] == 'dark' )   _colorOverlay = 'overlay-dark overlay-opacity-2';
            else if( s[0] == 'light' )  _colorOverlay = 'overlay-light overlay-opacity-7';
        }

        // Icon Color
        _colorIcon = ( typeof s[1] !== 'undefined' ) ? s[1] : _colorIcon;

      }


      if(option === 'show') {

        // remove existing and stop
        if(jQuery('#js_loading_icon').length > 0) {
          jQuery('#js_loading_icon').remove();
          return;
        }

        // 1. overlay, absolute positioning inside container
        var tplOverlay = '<div id="js_loading_icon" class="position-absolute absolute-full ' + _colorOverlay + ' z-index-9999 text-center">' 
                            + '<i class="' + $.SOW.config.sow__icon_loading + ' fs-3 '+_colorIcon+' valign-middle"></i>'
                       + '</div>';

        // 2. fixed - bottom of the screen, no overlay
        var tplIcon = '<div id="js_loading_icon" class="position-fixed fixed-bottom w-100 mb-3 z-index-9999 text-center shadow-none">'
                          + '<span class="bg-white d-inline-block px-4 py-1 rounded shadow-lg">'
                              + '<i class="' + $.SOW.config.sow__icon_loading + ' fs-3 '+_colorIcon+'"></i>'
                          + '</span>'
                    + '</div>';


        var _tpl        = (_layout == 'overlay') ? tplOverlay : tplIcon;
        var _container  = (_layout == 'overlay') ? _container : 'body'; // it's fixed, add to body!


        // show
        _container = (typeof _container === 'object') ? _container : jQuery(_container);
        _container.prepend(_tpl);


      } else {

        jQuery('#js_loading_icon').remove();

      }

    },




    /**
     *
     *  @executeFunctionByName
     *
        $.SOW.helper.executeFunctionByName("FunctionName", window);
        $.SOW.helper.executeFunctionByName("My.Namespace.functionName", window, arguments);
        $.SOW.helper.executeFunctionByName("Namespace.functionName", My, arguments);


        !WARNING! !NOTE!

        Most js code obfuscators might wreck the code as they will change the function names, making the string invalid.
        Anyway, all obfuscators will double the size of your code and, of course, will be much slower!

        This function is used in two places:
            1. init|reinit scripts
            2. ajax callbacks

        Please do not overuse it!

     *
     **/
    executeFunctionByName: function(functionName, context /*, args */) {

        // return new Promise(resolve => {

            if(typeof(window) !== 'undefined') {

                // Use the window (from browser) as context if none providen.
                context = context || window;
            
            } else {

                // If using Node.js, the context will be an empty object
                context = context || global;

            }


            var args        = Array.prototype.slice.call(arguments, 2);
            var namespaces  = functionName.split(".");
            var func        = namespaces.pop();

            for(var i = 0; i < namespaces.length; i++) {
                context = context[namespaces[i]];
            }


            return context[func].apply(context, args);

        // });

    },




    /**
     *
     *  @overlay
     *
        $.SOW.helper.overlay('show|hide|toggle');
     *
     **/
    overlay: function(option) {

        if(option === 'show') {

            jQuery('body').append('<div id="overlay-default"></div>');
            jQuery('body').addClass('overflow-hidden');

        }

        else if(option === 'hide') {

            jQuery('#overlay-default').unbind().remove();
            jQuery('body').removeClass('overflow-hidden');
    
        }

        else {

            if(jQuery('#overlay-default').length > 0) {
                $.SOW.helper.__overlay_hide();
            } else {
                $.SOW.helper.__overlay_show();
            }

        }

    },
        __overlay_show: function() {
            jQuery('body').append('<div id="overlay-default"></div>');
            jQuery('body').addClass('overflow-hidden');
        },
        __overlay_hide: function() {
            jQuery('#overlay-default').unbind().remove();
            jQuery('body').removeClass('overflow-hidden');
        },




    /**
     *
     *  @randomStr
     *
        $.SOW.helper.randomStr(8, ''|L|N);
     *
     **/
    randomStr: function(length, type) {

        switch(type) {

            case 'L':
                var characters   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;

            case 'N': 
                var characters   = '0123456789';
                break;

            default:
                var characters   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        }

        var result           = '';
        var charactersLength = characters.length;

        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }

        return result;

    },


    /**
     *
     *  @byte2size
     *
     *  $.SOW.helper.byte2size(bytes, precision=2, int_only=false);
     *
     **/
    byte2size: function(bytes, precision, int_only) {

      var precision   = typeof precision  !== 'undefined' ? precision : 2;
      var int_only    = typeof int_only   !== 'undefined' ? int_only  : false;

      if(bytes < 1) 
              return 0 + (int_only === false) ? 'B' : '';


      var k           = 1024;
      var precision   = precision < 0 ? 0 : precision;
      var unit        = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

      var i           = Math.floor(Math.log(bytes) / Math.log(k));
      var unit_txt    = (int_only === false) ? ' ' + unit[i] : 0;
      return parseFloat((bytes / Math.pow(k, i)).toFixed(precision)) + unit_txt;

    },




    /**
     *
     *  @byte2kb
     *
     *  $.SOW.helper.byte2kb(bytes);
     *
     **/
    byte2kb: function(bytes) {

      if(bytes < 1) return bytes;

      var bytes = bytes / 1024;
      return (Math.round(bytes * 100) / 100);

    },





    /**
     *
     *  @scrollAnimate
     *
        $.SOW.helper.scrollAnimate(_el, _offset, _hash_change, _speed);
            _el             = element to scroll to. #top = page top
            _offset         = top offset (0 default)
            _hash_change    = update url hash on click
            _speed          = scroll speed (400 default)

        $.SOW.helper.scrollAnimate('#top', 0, false, 400);
     *
     **/
    scrollAnimate: function(_el, _offset, _hash_change, _speed) {

      _el                 = typeof _el            !== 'undefined'     ? _el           : '';
      _hash_change        = typeof _hash_change   !== 'undefined'     ? _hash_change  : 'false';
      _offset             = typeof _offset        !== 'undefined'     ? _offset       : 0;
      _speed              = typeof _speed         !== 'undefined'     ? _speed        : 400;

      // Calculate offset if not given!
      if(_offset < 1) {

        if($.SOW.globals.elBody.hasClass('header-hide'))
            _offset = 15;

        // .header-fixed is added by js header plugin for all: .header-sticky, .header-scroll-reveal
        else if($.SOW.globals.elBody.hasClass('header-fixed') || $.SOW.globals.elBody.hasClass('header-sticky'))
            _offset = $.SOW.globals.elHeader.outerHeight() + 15;

      }


      // Scroll
      if(_el != '#' && _el != '#!' && _el != 'javascript:;') {

          if(_el == '#top') {

              jQuery('html, body').animate({scrollTop: $.SOW.globals.elBody.offset().top}, _speed, function() {

                  if(_hash_change == 'true') {
                      window.location.hash = _el;
                  }

              });

          } else {

              // unexpected error (should never happen - invalid element)!
              if(!jQuery(_el).offset()) return;

              jQuery('html, body').animate({scrollTop: jQuery(_el).offset().top - parseInt(_offset)}, _speed, function() {

                  if(_hash_change == 'true') {
                      window.location.hash = _el;
                  }

              });

          }

      }

    },



    /**
     *
     *  @removeUrlHash
     *
        $.SOW.helper.removeUrlHash('https://domain.com/url#hash');
     *
     **/
    removeUrlHash: function(_url){

      if (_url.indexOf('#') > -1)
        return _url.replace(/#.*$/, '');

      return _url;

    },



    /**
     *
     *  @playSound
     *
        $.SOW.helper.playSound('path/to/audio.mp3');
     *
     **/
    playSound: function(_soundFile) {

      var audioElement = document.createElement('audio');

      audioElement.setAttribute('src', _soundFile);
      audioElement.setAttribute('autoplay', 'autoplay');

      audioElement.addEventListener("load", function() {
        audioElement.play();
      }, true);

    },





    /**
     *
     *  @time_from_ms
     *  
        $.SOW.helper.time_from_ms(miliseconds, 's|m|h|d|empty for all');
     *
     **/
    time_from_ms: function(miliseconds, format) {
      var days, hours, minutes, seconds, total_hours, total_minutes, total_seconds;

      total_seconds   = parseInt(Math.floor(miliseconds / 1000));
      total_minutes   = parseInt(Math.floor(total_seconds / 60));
      total_hours     = parseInt(Math.floor(total_minutes / 60));
      days            = parseInt(Math.floor(total_hours / 24));

      seconds         = parseInt(total_seconds % 60);
      minutes         = parseInt(total_minutes % 60);
      hours           = parseInt(total_hours % 24);

      switch(format) {
        case 's': return total_seconds;
        case 'm': return total_minutes;
        case 'h': return total_hours;
        case 'd': return days;
        default:  return { d: days, h: hours, m: minutes, s: seconds };
      }
    },



    /**
     *
     *  @get_browser
     *
        $.SOW.helper.get_browser();
     *
     **/
    get_browser: function() {

      var ua = navigator.userAgent.toLowerCase(); 

           if (ua.indexOf('chrome') > -1)   return 'chrome';
      else if (ua.indexOf('safari') > -1)   return 'safari';
      else if (ua.indexOf('mozilla') > -1)  return 'mozilla';
      else if (ua.indexOf('edge') > -1)     return 'edge';
      else return 'n/a'; // ie, etc

    },




    /**
     *
     *  @params_parse
     *
        var params = $.SOW.helper.params_parse('['param','value']['param2','value2']); // return: array
     *
     **/
    params_parse: function(string) {

      if(string != '' && string.length > 2) {

        // creating a valid json
        var string = '[' + string + ']';                // add [] brackets
        var string = string.replace(/'/g, '"');         // replace ' with "
        var string = string.replace(/ /g, '');          // remove spaces
        var string = string.replace(/]\[/g, '],[');     // replace: '][' with '],['

        // parse 
        var string = JSON.parse(string);

      }

      return string;

    },




    /**
     *
     *  @currencyFormat
     *
        $.SOW.helper.currencyFormat(1000000); // output: 1,234,567.89

        // 1,234,567.89
        $.SOW.helper.currencyFormat(1000000, [
               2, ',', '.' // en
            // 2, '.', ',' // de
            // 2, ' ', ',' // fr
        ]);

     *
     **/
    currencyFormat: function(amount, custom) {

      if(typeof custom !== 'object')
        var custom = [
               2, ',', '.' // en
            // 2, '.', ',' // de
            // 2, ' ', ',' // fr
        ];

      return (  amount.toFixed(custom[0])
                  .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1:repl:')
                  .replace('.', custom[2])
                  .replace(/:repl:/g, custom[1])
      );

    },




    /** 

      Get JS/CSS Location
      
      $.SOW.helper.scriptLocation( 'css' );
      $.SOW.helper.scriptLocation( 'js' );

        [Aliases]
        $.SOW.helper.jsLocation(); // output: javascript location
        $.SOW.helper.cssLocation(); // output: css location

    **/
    jsLocation: function()  { return $.SOW.helper.scriptLocation( 'js' ); },
    cssLocation: function() { return $.SOW.helper.scriptLocation( 'css' ); },
    scriptLocation: function( sriptType ) {

      let curScript;
      let selector = (sriptType == 'js') ? 'script[src]' : 'link[rel="stylesheet"]';
      let scripts  =  document.querySelectorAll( selector );

      for(let i = 0; i < scripts.length; i++) {

        // set as default
        curScript = (sriptType == 'js') ? scripts[i].src : scripts[i].href ;

             if(curScript.indexOf('core') !== -1)      break;
        else if(curScript.indexOf('vendor') !== -1)    break;
        else if(curScript.indexOf('bundle') !== -1)    break;
        else if(curScript.indexOf('theme') !== -1)     break;

        // nothing found, reset back
        curScript = null;

      }

      // nothing we want found! get the last script!
      if(!curScript)
        curScript = (sriptType == 'js') ? scripts[scripts.length-1].src : scripts[scripts.length-1].href;

      if(!curScript)
        curScript = (sriptType == 'js') ? 'assets/js/' : 'assets/css/';

      let curScriptChunks = curScript.split('/');
      let curScriptFile   = curScriptChunks[curScriptChunks.length - 1];
      let scriptPath      = curScript.replace(curScriptFile, '');

      return scriptPath;

  },



    /**
     *
     *  @strhash
     *  author: Sergey.Shuchkin
     *  https://stackoverflow.com/questions/6122571/simple-non-secure-hash-function-for-javascript
     *
        $.SOW.helper.strhash('string here');
     *
     **/
    strhash: function( str ) {
      if (str.length % 32 > 0) str += Array(33 - str.length % 32).join("z");
      
      var hash = '', bytes = [];
      var i, j, k, a; i = j = k = a = 0;
      var dict = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','1','2','3','4','5','6','7','8','9'];
      
      for (i = 0; i < str.length; i++ ) {
        var ch = str.charCodeAt(i);
        bytes[j++] = (ch < 127) ? ch & 0xFF : 127;
      }

      var chunk_len = Math.ceil(bytes.length / 32);   
     
      for (i=0; i<bytes.length; i++) {
        j += bytes[i]; k++;
       
        if ((k == chunk_len) || (i == bytes.length-1)) {
          var a = Math.floor( j / k );

          if (a < 32)             hash += '0';
          else if (a > 126)       hash += 'z';
          else                    hash += dict[  Math.floor( (a-32) / 2.76) ];

          var j = k = 0;
        }
      }

      return hash;

    },




    /**
     *
     *  @jsonParse
     *
     *
        $.SOW.helper.jsonParse('object/string here');
     *
     **/
    jsonParse: function( data ) {

      // check
      if( data == '' || typeof data === 'object' )
        return data;

      // parse json
      try {

        var _data = JSON.parse( data );

      } catch(err) {

        var _data = data;

      }

      // return
      return (typeof _data === 'undefined' || _data.length < 1) ? null : _data;

    },




    /**
     *
     *  @serializeArray
     *
     *
        $.SOW.helper.serializeArray(form);
     *
     **/
    serializeArray: function( form ) {

      if( jQuery() )
        return jQuery( form ).serializeArray();


      // --


      var form    = (typeof form === 'object') ? form : document.querySelector( form ),
          arr     = [];

      Array.prototype.slice.call(form.elements).forEach(function (field) {

        if (!field.name || field.disabled || ['file', 'reset', 'submit', 'button'].indexOf(field.type) > -1) 
          return;

        if (field.type === 'select-multiple') {

          Array.prototype.slice.call(field.options).forEach(function (option) {
              
            if (!option.selected) 
              return;
            
            arr.push({
              name: field.name,
              value: option.value
            });

          });

          return;
        }

        if (['checkbox', 'radio'].indexOf(field.type) >-1 && !field.checked) 
            return;

        arr.push({

          name:   field.name,
          value:  field.value

        });

      });

      return arr;

    },




    /**
     *
     *  @compareArray
     *  Return: true|false
     *
        $.SOW.helper.compareArray(array1, array2);
        $.SOW.helper.compareArray([2,3,1,4], [1,2,3,4]);
     *
     **/
    compareArray: function( array1, array2 ) {

      const array2Sorted = array2.slice().sort();

      return array1.length === array2.length && array1.slice().sort().every(function(value, index) {
        return value === array2Sorted[index];
      });

    },




    /**
     *
     *  @consoleLogReinit
     *
        $.SOW.helper.consoleLogReinit(scriptInfo, selector);
     *
     **/
    consoleLogReinit: function(scriptInfo, selector) {

      $.SOW.helper.consoleLog('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
      $.SOW.helper.consoleLog(scriptInfo, 'color: #6dbb30; font-weight: bold; font-size:14px;');
      $.SOW.helper.consoleLog('Ajax Reinit For: ' + selector);
      $.SOW.helper.consoleLog(window.location.href);
      $.SOW.helper.consoleLog('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');

    },



    /**
     *
     *  @consoleLog
     *
        $.SOW.helper.consoleLog('%cLorem Ipsum', 'color: #ff0000;');
     *
     **/
    consoleLog: function(data, css) {

      if($.SOW.config.sow__debug_enable !== true)
        return;

      // Console css
      if(typeof css !== "undefined" && typeof css !== 'object') {
        var data    = '%c' + data;
        console.log(data, css);
        return;
      }

      else if(typeof css === 'object') {
        console.log(data, css);
        return;
      }

      console.log(data);

    }

  };
})(jQuery);

/**
 *
 *  [SOW] Ajax Navigation
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.ajax_navigation.init('a.js-ajax');
 *                  -- + --
 *                  From another plugin, for a specific container only:
 *                  Only this plugin is reinitialised, -WITHOUT- $.SOW.reinit() call 
 *                  $.SOW.core.ajax_navigation.__initFor('#container');
 *                  -- + --
 *                  $.SOW.core.ajax_navigation.__selector();
 *                  returns the selector only: .js-ajax
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Ajax Navigation';

  window.sow_ajax_links_last_href     = null;
  window.sow_ajax_links_curr_href     = null;


  $.SOW.core.ajax_navigation = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {

      /* 

          You can overwrite by adding the needed param to the link.

          These params can be used on any <a class="js-ajax" ...></a> to overwrite thse settings
              data-ajax-method="GET|POST"
              data-ajax-container="#middle"                   container to push data from the server; admin use #middle as main content container
              data-ajax-update-url="true|false"               update history state.
              data-ajax-show-loading-icon="true"              false to disable loading icon on ajax requests
              data-ajax-callback-function=""                  custom callback function
              data-ajax-callback-before-push="false"          do not push data to container, send to the callback only
              data-ajax-reponse-extract-element="#middle"     get a specific element from returned data (not the entire result/page). Example: <div id="something"><div class="else"> <div id="middle">this content</div> </div></div>
              data-ajax-autoscroll-to-content="false"         scroll to top content after loading

              Second container, if needed to add a class: hide, show, animate, etc
              data-ajax-container2-element="img.js-ajax-loaded-animate"   <img class="js-ajaxloaded-animate" ...>
              data-ajax-container2-class="animate-bouncein"

      */
      enable                          : true,
      method                          : 'GET',
      contentType                     : '',
      dataType                        : '',
      headers                         : '',
      crossDomain                     : '',
      data_params                     : {ajax:'true'},

      target_container                : '#middle', // where data is pushed
      update_url                      : true,
      show_loading_icon               : true,

      /* 
          callback_example = function(el, data, target_container) {
              // el               = link clicked              $(this)
              // data             = server response           (html|string)
              // target_container = container to push data    (string:#middle)
          }
      */
      callback_function               : '',

      // content (server response) is sent to your callback function only.
      callback_before_push            : false,

      /*
          #middle         = main content to be extracted
          #page_js_files  = second container containing js files wrapped inside a div
      */
      parser_extract_enable           : true,
      parser_force_push2target        : false,        // in case #middle element not found in request, entire data/page is pushed/shown
      parser_extract_element          : '#middle, #page_js_files',

      autoscroll_to_content           : true,         // scroll up to content after page loaded


      // Optional second container - add a class when page loaded
      container2_element              : '',
      container2_class                : '',

      // 404 error page
      page_404                        : '404.html',


      // Reload page content on back/forward history button instead of loading from history cache
      onpopstate_reload               : true,

      /*
          Different than callback, cannot be overwrited by params
          Will call a custom function after each page load
          Condition: callback_before_push: false (else, call the function from your callback).
          
          Can be used to reinit custom scripts/plugins and/or process special data

          custom_function_example = function(el, target_container) {
              // el           = link clicked                      $(this)
              // container    = container where data is pushed    (string:#middle)
          }
      */
      custom_function_call            : '',




      // Ajax remainings of other plugins that are not self removed
      // .note-popover = summernote (bootstrap4 bug)
      AjaxGarbage                     : '', // reserved to GULP config
      AjaxOtherGarbage                : '.datepicker, .popover, .daterangepicker, .modal, .modal-backdrop, .note-popover, #dropdown_toggle_overlay, .tooltip',
  
    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      /* weberver required */
      if( window.location.origin == 'file://' ) 
        return;

      /* disabled by config */
      if($.SOW.core.ajax_navigation.config.enable !== true) {
        $.SOW.helper.consoleLog('Disabled : ' + scriptInfo, 'color: #ff0000;');
        return;
      }


      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.selector_orig      = __selector[2];    // $('#selector') // without ajax container prefix
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;

      if(jQuery(this.selector).length < 1)
        return;


      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      /*

          History Postate

      */
      $.SOW.core.ajax_navigation.__historyPopState();



      // 1. Has no selector
      if(!this.selector) {
        $.SOW.core.ajax_navigation.ajax_navigation($('a.js-ajax'));
        return this.collection;
      }

      // 2. Has selector
      return this.collection.each(function() {
          
        $.SOW.core.ajax_navigation.ajax_navigation($(this));

      });

    },



    /**
     *
     *  @ajax_navigation
     *
        1. LINKS

            USAGE 1:

                <a href="https://myurl.com/request/" class="js-ajax" data-ajax-container="#my_container">Click</a>
                <div id="my_container"><!-- ajax content is loaded here --></div>

            USAGE 2: (using data-href)

                <a href="#" data-href="https://myurl.com/request/" class="js-ajax" data-ajax-container="#my_container">Click</a>
                <div id="my_container"><!-- ajax content is loaded here --></div>


            OPTIONAL PARAMS:
                data-ajax-method="GET|POST"             default: GET
                data-ajax-update-url="true"     default: false - update history state
                data-ajax-show-loading-icon="false"         do not show loading icon
                data-ajax-callback-function=""          custom callback function
                data-ajax-callback-before-push="false"  do not push data to container, send to the callback only
                data-ajax-reponse-extract-element="#middle"     push only an element returned from ajax (not the entire result/page)
                data-ajax-autoscroll-to-content="false"     scroll to top content after loading
                data-ajax-container="#middle"           container to push data from the server

                Second container, if needed to add a class: hide, show, animate, etc
                data-ajax-container2-element="#myimg"
                data-ajax-container2-class="animate-bouncein"

            &ajax=true - added but NOT to .html files
     *  
     *
     **/
    ajax_navigation: function(_this, ajax_navigation_reinit_skip) { // ajax_navigation_reinit_skip: used by calling from other plugins via __initFor(). DO NOT REMOVE!

      var _selector       = this.selector;
      var _selector_orig  = this.selector_orig;

      if($.SOW.core.ajax_navigation.config.enable !== true) {
        _this.off('click');
        return;
      }

      // !!! do not use off()
      // deep navigation links conflict for ajax
      _this.on('click', function(e) {


        /* 
            Because we want to be sure that we bind only once
            and avoid multiple times! We can't use unbind() because
            some other plugins might bind that link for some reason!
        */
        // if(jQuery(this).hasClass('js-ajaxified'))
        //  return;
        /* -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */


        e.preventDefault();

        // Bootstrap error!
        if( !jQuery(this).hasClass('dropdown-item') )
          e.stopPropagation();

        var _t                      = jQuery(this),
            _href                   = _t.attr('href')                               || '#',
            _href_data              = _t.attr('data-href')                          || '',
            _href_title             = _t.attr('title')                              || '',      // used by historyPushState to update the title
            _scrollToContent        = _t.attr('data-ajax-autoscroll-to-content')    || '',
            _callback               = _t.data('ajax-callback-function')             || '',
            _callbackBeforePush     = _t.attr('data-ajax-callback-before-push')     || '',      // pass to calback, do not push content to container
            _method                 = _t.data('ajax-method')                        || '',
            _updateURL              = _t.attr('data-ajax-update-url')               || '',      // istory push state
            _showLoadingIcon        = _t.attr('data-ajax-show-loading-icon')        || '',
            _container2El           = _t.data('ajax-container2-element')            || '',      // add a class to a container, or an animation... anything
            _container2Class        = _t.data('ajax-container2-class')              || '',      // add a class to a container, or an animation... anything
            _contentResponseExtract = _t.data('ajax-parser-extract-element')        || '',      // id|class (document container to get data from)
            _target                 = _t.data('ajax-target-container')              || '';


        // data-href="..."
        // Is +always+ used if exists!
        if(_href_data != '')
          _href = _href_data;


        // Check once, use multiple times!
        var _hrefValid = (_href == '' || _href == '#' || _href == '#!' || _href == 'javascript:;' || _href == 'void(0)') ? false : true;


        // ++ Fullajax Navigation
        //      + We have ajax links inside a dropdown!
        //      + Close/hide the dropdown on click!
        if(_hrefValid === true && $.SOW.globals.is_mobile === false) {

          if(typeof $.SOW.core.dropdown === 'object')
            $.SOW.core.dropdown.forceCloseDropdown(_t);

        }




        // ok, bind identifier for this function so we don't bind again!
        //_t.addClass('js-ajaxified');



        // Check nav-deep for this link, if comes from content!
        // We want it highlighted!
        if(!_t.hasClass('nav-link') && typeof $.SOW.core.nav_deep === 'object') {

          /**
              
              We remove #hash and rewrite the _href
              Actually, the back button on pushState will not contain the #hash anymore
              But when the user click "Back Button", page position is ok
              It's not something critical, we can live with this intentional bug.
              The chance that somebody notice... is very, very, very low :)

          **/
          var _href               = $.SOW.helper.removeUrlHash(_href); // remove hash (helper)
          var __navHasThisLink    = jQuery(_selector_orig+'.nav-link[href="'+_href+'"]');

          if(__navHasThisLink.length > 0) {

            $.SOW.core.nav_deep.nav_deep_open(__navHasThisLink);
            $.SOW.core.nav_deep.nav_deep_close_all(__navHasThisLink);

          }

          // clear/reset
          var __navHasThisLink    = null;

        } 
        // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --



        /**
            CONFIG DEFAULTS
        **/
        if(_scrollToContent == '')
          var _scrollToContent = $.SOW.core.ajax_navigation.config.autoscroll_to_content;

        if(_callback == '')
          var _callback = $.SOW.core.ajax_navigation.config.callback_function;

        if(_callbackBeforePush == '')
          var _callbackBeforePush = $.SOW.core.ajax_navigation.config.callback_before_push;

        if(_method == '')
          var _method = $.SOW.core.ajax_navigation.config.method;

        if(_updateURL == '')
          var _updateURL = $.SOW.core.ajax_navigation.config.update_url;

        if(_showLoadingIcon == '')
          var _showLoadingIcon = $.SOW.core.ajax_navigation.config.show_loading_icon;

        if(_container2El == '')
          var _container2El = $.SOW.core.ajax_navigation.config.container2_element;

        if(_container2Class == '')
          var _container2Class = $.SOW.core.ajax_navigation.config.container2_class;

        // --

        if(_contentResponseExtract == '' && $.SOW.core.ajax_navigation.config.parser_extract_enable === true) /* default self config (se top config) */
          var _contentResponseExtract = $.SOW.core.ajax_navigation.config.parser_extract_element;

        /*
            
            In case there is another ajax request, 
            but for a different specified #destination_container

        */
        if(_target != '' && _target != _contentResponseExtract)
          _contentResponseExtract = '';

        // --

        if(_target == '')
          var _target = $.SOW.core.ajax_navigation.config.target_container;



        if(_target == '') {

          $.SOW.helper.consoleLog('Ajax request: data-ajax-container missing!');
          return;

        }


        else if(_hrefValid !== true) {

          $.SOW.helper.consoleLog('Ajax request: no valid link!');
          return;

        }




        /*
            
            Block "flooding"

        */
        if(window.sow_ajax_links_last_href == _href)
            return;

        window.sow_ajax_links_last_href = _href;
        // --



        // Remove any unwanted stuff - like: if click was from modal
        // jQuery('.modal-backdrop.show').remove();
        // $.SOW.globals.elBody.removeClass('modal-open overflow-hidden');
                    
        // Ajax remainings of other plugins that are not self removed
        $.SOW.core.ajax_navigation.__cleanAjaxGarbage();
        // ----------------------------------------------------------





        /*

            CREATE AN OBJECT WITH ALL PARAMS TO SEND THEM TO AJAX FUNCTION!

            The reason we do this, is because we need the exactly same
            ajax request for historyPushState on "back button" click.
            This object we save it as "html" (json) to historyPushState

            This object will be packed as json by __ajaxLinkProcess() function
             _json = JSON.stringify(_obj);

        */
        var _obj                            = new Object();
            _obj._t                         = _t;
            _obj._href                      = _href;
            _obj._scrollToContent           = _scrollToContent;
            _obj._callback                  = _callback;
            _obj._callbackBeforePush        = _callbackBeforePush;
            _obj._method                    = _method;
            _obj._updateURL                 = _updateURL;
            _obj._showLoadingIcon           = _showLoadingIcon;
            _obj._container2El              = _container2El;
            _obj._container2Class           = _container2Class;
            _obj._contentResponseExtract    = _contentResponseExtract;
            _obj._target                    = _target;


        /**

            HOW IT WORKS?

                1. __ajaxLinkProcess() send an ajax request. If _callbackBeforePush is not set tot true, the page will update.
                2. the object above is packed into a JSON string and passed it to __ajaxLinkProcess() as to keep it in history pushState ;)
                3. user is happy, drinking 10 beers but suddently navigate back using "back button" so the page is the same but the link is changing! 
                4. the history will return the sting from cache (our jsonified object) we just created on step 2
                5. history function will call __ajaxLinkProcess() passing the object but disabling things like _updateURL (because is already on that page)
                6. __ajaxLinkProcess() will try to find the link to get the element because jQuery(this) is needed (I mean: _t)
                7. Because we keep the previous pages in pushHistory, we use them to catch the clicked link.

                Looks complicated but the process is simple.

        **/
        return $.SOW.core.ajax_navigation.__ajaxLinkProcess(_obj, ajax_navigation_reinit_skip);


      });

    },

        /**
         *
         *  Ajax Process
         *  :: Helper
         *
         **/
        __ajaxLinkProcess: function(_obj, ajax_navigation_reinit_skip) {

          if($.SOW.core.ajax_navigation.config.enable !== true)
            return;


          var _t                          = _obj._t,
              _href                       = _obj._href,
              _scrollToContent            = _obj._scrollToContent,
              _callback                   = _obj._callback,
              _callbackBeforePush         = _obj._callbackBeforePush,
              _method                     = _obj._method,
              _updateURL                  = _obj._updateURL,
              _showLoadingIcon            = _obj._showLoadingIcon,
              _container2El               = _obj._container2El,
              _container2Class            = _obj._container2Class,
              _contentResponseExtract     = _obj._contentResponseExtract,
              _target                     = _obj._target,
              _obj                        = null;



          // Yeah, the ugliest bool fix ever.
          var _callbackBeforePush = _callbackBeforePush+'';
          var _callbackBeforePush = _callbackBeforePush.toLowerCase();
          var _updateURL          = _updateURL+'';
          var _updateURL          = _updateURL.toLowerCase();
          var _showLoadingIcon    = _showLoadingIcon+'';
          var _showLoadingIcon    = _showLoadingIcon.toLowerCase();
          var _scrollToContent    = _scrollToContent+'';
          var _scrollToContent    = _scrollToContent.toLowerCase();



          jQuery.ajax({

              url:            _href,
              data:           $.SOW.core.ajax_navigation.config.data_params,
              type:           _method,
              contentType:    $.SOW.core.ajax_navigation.config.contentType,
              dataType:       $.SOW.core.ajax_navigation.config.dataType,
              headers:        $.SOW.core.ajax_navigation.config.headers,
              crossDomain:    $.SOW.core.ajax_navigation.config.crossDomain,

              beforeSend: function() {

                if(_showLoadingIcon == 'true')
                  $.SOW.helper.loadingSpinner('show', _target);
                
                // disable link
                _t.addClass('disabled');


                /*

                    Click from admin : close mobile aside

                */
                if($.SOW.globals.is_mobile === true) {

                  if($.SOW.globals.elBody.hasClass('layout-admin') && jQuery('#aside-main').hasClass('js-aside-show'))
                    jQuery('.btn-sidebar-toggle').trigger('click');

                }

                // Close any open dropdown!
                // BS5 error
                // $('.dropdown-menu.show').dropdown('hide').parent().find('[data-toggle="dropdown"]').attr('aria-expanded', 'false').removeClass('active');

              },

              error:  function(XMLHttpRequest, textStatus, errorThrown) {

                // 404 error page
                var page_404_response = $.SOW.core.ajax_navigation.page_404(_contentResponseExtract, _target); // should return true!

                if(!page_404_response) {

                  $.SOW.helper.loadingSpinner('hide');

                  if(typeof $.SOW.core.toast === 'object') {

                    $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!', 'bottom-center', 0, true);

                  } else {

                    alert("[404] Unexpected internal error!");

                  }

                }

              },

              success: function(data) {


                // Ajax remainings of other plugins that are not self removed
                $.SOW.core.ajax_navigation.__cleanAjaxGarbage();
                // ----------------------------------------------------------


                var __dataHtml = '';

                if(_callbackBeforePush == 'false') {

                    // Push data
                    if(_contentResponseExtract == '' || $.SOW.core.ajax_navigation.config.parser_extract_enable === false) {



                        /*
                            
                            [Deprecation] Synchronous XMLHttpRequest on the main thread is deprecated 
                            because of its detrimental effects to the end user's experience. 
                            For more help, check https://xhr.spec.whatwg.org/

                            Remove any <script> and append them using async:
                                html_parsed__apply_scripts()

                        */
                        var script_src          = new Array,
                            script_inline       = new Array,
                            jQ_source = jQuery('<div>' + data + '</div>');

                        // get scripts with src
                        jQuery('script[src]', jQ_source).each(function() {
                            script_src.push($(this).attr('src'));
                        });
                        jQuery('script[src]', jQ_source).remove();

                        // get scripts without src
                        jQuery('script', jQ_source).each(function() {
                            script_inline.push($(this));
                        });
                        jQuery('script', jQ_source).remove();
                        // --------------------------------------------------------------------------------
                        



                        /* ++ SEO ++
                            
                            Dedicated Tags
                            <meta-title>...</meta-title>
                            <meta-description>...</meta-description>
                            <meta-canonical>...</meta-canonical>
                            <meta-robots>...</meta-robots>

                        */
                        var seo_meta_title          = jQ_source.find('title').html() || jQ_source.find('meta-title').html(),
                            seo_meta_description    = jQ_source.find("meta[name=description]").attr("content") || jQ_source.find('meta-description').html(),
                            seo_meta_canonical      = jQ_source.find("link[rel=canonical]").attr("href") || jQ_source.find('meta-canonical').html(),
                            seo_meta_robots         = jQ_source.find("meta[name=robots]").attr("content") || jQ_source.find('meta-robots').html();

                        // Remove by default
                        jQuery('link[rel=canonical], meta[name=robots]').remove();

                        // Safe mode : update metas
                        if(seo_meta_title)
                            document.title = seo_meta_title;

                        if(seo_meta_description)
                            document.querySelector('meta[name="description"]').setAttribute("content", seo_meta_description);

                        if(seo_meta_canonical)
                            jQuery('head').append('<link rel="canonical" href="'+seo_meta_canonical+'">');

                        if(seo_meta_robots)
                            jQuery('head').append('<meta name="robots" content="'+seo_meta_robots+'">');

                        // Remove dedicated tags
                        jQuery('meta-title, meta-description, meta-canonical, meta-robots', jQ_source).remove();
                        // ++ ++ ++ ++


                        var parsed_data = jQ_source.html();


                        // force push content, as it is because nothing parsed
                        if(parsed_data == '' && $.SOW.core.ajax_navigation.config.parser_force_push2target === true)
                            var parsed_data = data;


                        // Append data
                        jQuery(_target).empty().append(parsed_data);


                        // Apply JS
                        $.SOW.core.ajax_navigation.html_parsed__apply_scripts(script_src, script_inline);
                        var jQ_source = null;


                        if(_updateURL == 'true')
                            var __dataHtml = parsed_data;

                    // Parse content and push
                    } else {

                        // Pase data
                        var _objParsed = $.SOW.core.ajax_navigation.html_parse(_contentResponseExtract, data);

                        if(typeof _objParsed !== 'object') {

                            var __dataHtml  = '';

                        } else {

                            var __dataHtml  = _objParsed.html_content;
                            var _href_title = _objParsed.meta_title;

                        }


                        // force push content, as it is because nothing parsed
                        if(!__dataHtml && $.SOW.core.ajax_navigation.config.parser_force_push2target === true)
                            __dataHtml = data;
                        

                        // Push Content
                        jQuery(_target).empty().append(__dataHtml);

                        // Push Scripts
                        $.SOW.core.ajax_navigation.html_parsed__apply_scripts(_objParsed.scripts_src, _objParsed.scripts_inline);
                        var _objParsed  = null; // clear

                    }
                    // --


                    // We also avoid adding same page to history pushState on multiple clicks on the same link
                    if(_updateURL == 'true' && window.sow_ajax_links_curr_href != _href) {

                        /*
                            
                            We create a new object here, can't use the one passed to the function!
                            Regular js function is working. This is not!
                        */
                        var _obj                        = new Object;
                        _obj._href                      = _href;
                        _obj._callback                  = _callback;
                        _obj._callbackBeforePush        = _callbackBeforePush;
                        _obj._contentResponseExtract    = _contentResponseExtract;
                        _obj._updateURL                 = _updateURL;
                        _obj._container2El              = _container2El;
                        _obj._container2Class           = _container2Class;
                        _obj._scrollToContent           = _scrollToContent;
                        _obj._target                    = _target;
                        _obj._html                      = (__dataHtml != '') ? __dataHtml : '';
                        var _json                       = JSON.stringify(_obj);

                        $.SOW.core.ajax_navigation.__historyPushState(_href, _href_title, _json); 
                    }


                    // scroll to content
                    if(_scrollToContent == 'true') {
                        if(typeof $.SOW.helper.scrollAnimate === "function") {
                            var _contentEl = jQuery(_target);
                            $.SOW.helper.scrollAnimate(_contentEl, 0, false, 200);
                        }
                    }


                    // Second container - remove and|or add class (animation, toggle, hide, etc)
                    if(_container2El != '') {

                        // to animate after scrolling
                        var _container2Delay = (_scrollToContent == true) ? 200 : 60;


                        // Remove first 
                        jQuery(_container2El).removeClass(_container2Class);

                        setTimeout(function() {

                            // Add back (useful for animation)
                            if(_container2Class != '')
                                jQuery(_container2El).addClass(_container2Class);

                        }, _container2Delay);

                    }


                    // custom_function_call()
                    // call this function after each page load
                    if($.SOW.core.ajax_navigation.config.custom_function_call != '') {

                        if(typeof $.SOW.helper.executeFunctionByName === "function")
                            $.SOW.helper.executeFunctionByName($.SOW.core.ajax_navigation.config.custom_function_call, window, _t, _target, this.selector);

                    }


                    // ++ REINIT PLUGINS ++
                    if(ajax_navigation_reinit_skip !== 'true')
                        $.SOW.core.ajax_navigation.reinit_call(_target);
                    // ++ ++ ++ ++ ++ ++ ++


                }



                // notice only
                if(_callbackBeforePush == 'true' && _callback == '')
                  $.SOW.helper.consoleLog('data-ajax-callback-function="" -BUT- data-ajax-callback-before-push="true"');



                // callback function
                if(_callback != '') {

                  if(typeof $.SOW.helper.executeFunctionByName === "function")
                    $.SOW.helper.executeFunctionByName(_callback, window, _t, __dataHtml || data, _target);

                }



                // remove loading icon
                $.SOW.helper.loadingSpinner('hide');



                // enable link
                _t.removeClass('disabled');



                // Add url to container, in case is "ajax content" plugin used
                // so we can have a real "reload btn" with new link
                if(jQuery(_target).hasClass('js-ajax'))
                  jQuery(_target).attr('data-ajax-url', _href);



                // clear/reset
                var data            = null, 
                    _obj            = null,
                    _json           = null,
                    __dataHtml      = null, 
                    __source        = null, 
                    __dataParsed    = null;
                window.sow_ajax_links_last_href     = null;

                // Used to avoid adding same page to history pushState on multiple clicks on the same link
                if(_updateURL == 'true' && window.sow_ajax_links_curr_href != _href)
                  window.sow_ajax_links_curr_href = _href;


                // Needed for links from fullscreen
                $.SOW.globals.elBody.removeClass('overflow-hidden');

              }

          });

          return true;

        },







    /**
     *
     *  @html_parse
     *  Extract content of html. One or more selectors
     *  html_parse('#selector1, #selector2, ...', html);
     *
     *
    **/
    html_parse: function(_selectors, _data) {

      if(!_data) return null;

      else if(!_selectors) {
        var _obj                    = new Object;
            _obj.meta_title         = null;
            _obj.meta_description   = null;
            _obj.html_content       = _data;
      }

      var jQ_source           = jQuery('<div>' + _data + '</div>'),
          html_content        = '',
          meta_title          = jQ_source.find('title').html() || jQ_source.find('meta-title').html(),
          meta_description    = jQ_source.find("meta[name=description]").attr("content") || jQ_source.find('meta-description').html(),
          data_parsed         = jQ_source.find(_selectors),
          script_src          = new Array,
          script_inline       = new Array;

      if(!data_parsed)
        return null;


      // Add each element (if more than one elements passed)
      data_parsed.each(function() {

        /*
            
            [Deprecation] Synchronous XMLHttpRequest on the main thread is deprecated 
            because of its detrimental effects to the end user's experience. 
            For more help, check https://xhr.spec.whatwg.org/

            Remove any <script> and append them using async:
                html_parsed__apply_scripts()

        */
        // get scripts with src
        jQuery('script[src]', this).each(function() {
            script_src.push($(this).attr('src'));
        });
        jQuery('script[src]', this).remove();

        // get scripts without src
        jQuery('script', this).each(function() {
            script_inline.push($(this));
        });
        jQuery('script', this).remove();

        // at this point, we shuld be <script> free
        html_content += $(this).html(); // required for __historyPushState

      });


      var _obj                    = new Object;
          _obj.meta_title         = meta_title;
          _obj.meta_description   = meta_description;
          _obj.html_content       = html_content;
          _obj.scripts_src        = script_src;
          _obj.scripts_inline     = script_inline;


      // Safe mode : update metas
      if(meta_title != '')
        document.title = meta_title;

      if(meta_description != '')
        document.querySelector('meta[name="description"]').setAttribute("content", meta_description);

      return _obj;

    },






    /**
     *
     *  @html_parsed__apply_scripts
     *  Apply parsed scripts
     *
     *
    **/
    html_parsed__apply_scripts: function(scripts_src, scripts_inline) {


      /**

          Reason this function exists:

              [Deprecation] Synchronous XMLHttpRequest on the main thread is deprecated 
              because of its detrimental effects to the end user's experience. 
              For more help, check https://xhr.spec.whatwg.org/

              So we can't just append(html_content) including scripts.
              We cut them out on html_parse() and append them this way!

      **/
      // $.SOW.helper.loadScript(script_arr, async[true|false], cache[true|false])
      $.SOW.helper.loadScript(scripts_src, false, true).done(function() {

        jQuery('#js_ajax_navigation_appended_scripts').remove();

        if(scripts_inline.length > 0) {

          $.SOW.globals.elBody.append('<div id="js_ajax_navigation_appended_scripts"></div>');

          for (var i in scripts_inline) {
            jQuery('#js_ajax_navigation_appended_scripts').append(scripts_inline[i]);
          }

        }

      });

      return true;

    },







    /**
     *
     *  @reinit_call
     *  Call main $.SOW.reinit()
     *
     *
    **/
    reinit_call: function(_target) {

      // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
      // console log
      $.SOW.helper.consoleLogReinit(scriptInfo, _target);
      // reinit inside ajax container
      $.SOW.reinit(_target);
      // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

    },








    /**
     *
     *  @page_404
     *  Get Page 404
     *
     *
    **/
    page_404: function(_contentResponseExtract, _target) {

      if($.SOW.core.ajax_navigation.config.page_404 == '')
        return null;

      // get the page
      jQuery.ajax({
          url:            $.SOW.core.ajax_navigation.config.page_404,
          data:           {ajax:"true"},
          type:           'GET',
          contentType:    '',
          dataType:       '',
          headers:        '',
          crossDomain:    '',

          beforeSend: function() {

            $.SOW.helper.loadingSpinner('show', _target);

          },

          error:  function(XMLHttpRequest, textStatus, errorThrown) {

            $.SOW.helper.loadingSpinner('hide');

          },

          success: function(data) {

            // Pase data
            var _objParsed  = $.SOW.core.ajax_navigation.html_parse(_contentResponseExtract, data),
                data        = null;

            if(typeof _objParsed !== 'object') {

              var __dataHtml = '';

            } else {

              var __dataHtml  = _objParsed.html_content;
              document.title  = _objParsed.meta_title;

              // we need it in case o refresh!
              $.SOW.core.ajax_navigation.__historyPushState($.SOW.core.ajax_navigation.config.page_404, document.title, __dataHtml);

            }


            // force push content, as it is because nothing parsed
            if(!__dataHtml && $.SOW.core.ajax_navigation.config.parser_force_push2target === true)
                __dataHtml = data;


            // Push Content
            jQuery(_target).empty().html(__dataHtml);
            $.SOW.helper.loadingSpinner('hide');

            // Push Scripts
            $.SOW.core.ajax_navigation.html_parsed__apply_scripts(_objParsed.scripts_src, _objParsed.scripts_inline);
            var _objParsed  = null; // clear

            // reinit plugins for 404 page
            $.SOW.core.ajax_navigation.reinit_call(_target);


          }

      });

      return true;

    },









    /*

        Init for specific container
        Used by other plugins

    */
    __initFor: function(_container) {

      if(!_container) return;
      var ajax_navigation_reinit_skip = true; // true, no matter what!
      return $.SOW.core.ajax_navigation.ajax_navigation(jQuery(_container + ' ' + this.selector_orig), ajax_navigation_reinit_skip);

    },







    /*

        Return the selector only
        Example: .js-ajax

    */
    __selector: function() {

      return this.selector_orig;

    },





    /*

        Clean `garbage`
        Ajax remainings that are not self removed

    */
    __cleanAjaxGarbage: function() {

      jQuery($.SOW.core.ajax_navigation.config.AjaxOtherGarbage).remove();
      $.SOW.globals.elBody.removeClass('overflow-hidden modal-open');

      // clean all active toasts
      if(typeof $.SOW.core.toast === 'object')
        $.SOW.core.toast.destroy();

      // clean user garbage
      if($.SOW.core.ajax_navigation.config.AjaxGarbage != '')
        jQuery($.SOW.core.ajax_navigation.config.AjaxGarbage).remove();
  
    },


















    /** HISTORY PUSH STATE
        Change url without refresh & push data
     **************************************************************** **/
    __historyPushState: function(_url, _pageTitle, html) {

      if(_url == '' || _url == '#' || _url == '#!' || _url == 'javascript:;' || _url == 'void(0)')
        return;

      // Non compatible browsers
      if(typeof(history.pushState) == "undefined") {
        window.location = _url;
        return;
      }

      window.history.pushState(
        {
            "html":         html,
            "pageTitle":    _pageTitle,
        },
        "", _url
      );

      $.SOW.helper.consoleLog("SOW Ajax : history.pushState : " + _url);

    },






    /*

        Popstate
        Back Button

    */
    __historyPopState: function() {

      var _selector       = this.selector;
      var _selector_orig  = this.selector_orig; // without ajax container

      if(typeof(window.onpopstate) !== "function") { 

        window.onpopstate = function(e) {

          if( e.state ) {

            if(typeof e.state.pageTitle !== 'undefined')
              document.title = e.state.pageTitle; // update title from history

            // if(typeof e.state.pageTitle !== 'undefined')
            // document.querySelector('meta[name="description"]').setAttribute("content", _objParsed.meta_description);

            var _obj = JSON.parse(e.state.html);

            // Update html (from history version)
            // needed to search the link - see below
            var _target = (_obj._target != '') ? _obj._target : '#middle';
            jQuery(_target).empty().html(_obj._html);

            // No reason to continue, we can't find the element to click or get data
            // We keep the page from history!
            if(_obj._href == '') {
              $.SOW.helper.consoleLog("SOW Ajax : history.pushState : Err : Missing Back Link! Displayed the page from history, no real request to the server reload!");
              return;
            }


            /* --------------------------------------------------------------------------- */

            // Let's get the element! We have only the link to work with!
            var _t = jQuery(_selector_orig+'[href="'+_obj._href+'"]').first();

            if(!_t)
              var _t = jQuery(_selector_orig+'[href="'+_obj._href+'"]').first();


            if(!_t) {

              $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!', 'bottom-center', 0, true);

              // No link found? Reload page if also no history html version.
              if(_obj._html == '') location.reload(true);
              return; // Ok, stop here!

            }

            /* --------------------------------------------------------------------------- */
            if($.SOW.core.ajax_navigation.config.onpopstate_reload === true) {
                
              /*
                  Reload the content!
                  Display the page from the history, which is already doing few lines above!
              */

              _obj._t                 = _t;           // most important (is actually the link clicked - dom element)
              _obj._updateURL         = false;        // do not update, we already are on it
              _obj._scrollToContent   = false;        // annoyng on each back to have a scrolling page
              
              $.SOW.core.ajax_navigation.__ajaxLinkProcess(_obj);                 // ajax call, refresh page|content
              $.SOW.helper.consoleLog("SOW Ajax : onpopstate : Content request sent to the server!");

            }
            /* --------------------------------------------------------------------------- */

            /*
                Deep Navigation Plugin
                Active Current
            */
            if(typeof $.SOW.core.nav_deep === 'object') {
              $.SOW.core.nav_deep.nav_deep_open(_t);
              $.SOW.core.nav_deep.nav_deep_close_all(_t);
            }

            /* --------------------------------------------------------------------------- */


          } else {

            /*
                
                onpopstate is handling hashed as "regular" links, refreshing the page
                So we have to stop this behavior!

            */
            var _href = window.location.href;
            if (_href.indexOf('#') > -1) return;

            // Origin - refresh!
            location.reload(true);

          }


        };

      }

    }


  }

})(jQuery);
/**
 *
 *  [SOW] Ajax Content
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.ajax_content.init('div.js-ajax');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo  = 'SOW Ajax Content';


  $.SOW.core.ajax_content = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {

      method                          : 'GET',
      contentType                     : '',
      dataType                        : '',
      headers                         : '',
      crossDomain                     : '',
      data_params                     : {ajax:'true'},

      /* 
          callback_example = function(el, data, target_container) {
              // el               = link clicked              $(this)
              // data             = server response           (html|string)
              // target_container = container to push data    (string:#middle)
          }
      */
      callback_function               : '',

      // content (server response) is sent to your callback function only.
      callback_before_push            : false,

    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.selector_orig      = __selector[2];    // $('#selector') // without ajax container prefix
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      if(jQuery(this.selector).length < 1)
        return;


      /* 
          
          Because this script is needed on body only and not inside itself,
          we block this script loading by another plugins reinit
          Easier and better than creating another rule in Controller for all plugins!


          Note: we use .last() instead of .first() because we might have
          one content in a static area (main navigation as an eample) 
          and another one inside ajax (loaded by ajax navigation)

      */
      if(jQuery(this.selector_orig).last().hasClass('js-ajaxified'))
        return;
      // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      // 1. Has no selector
      if(!this.selector) {
        $.SOW.core.ajax_content.ajax_content($('.js-ajax'));
        return this.collection;
      }

      // 2. Has selector
      return this.collection.each(function() {

        $.SOW.core.ajax_content.ajax_content($(this));

      });

    },



    /**
     *
     *  @ajax_content
     *
        2. DIVS (and sections)

            USAGE:

            <section id="section_1" class="js-ajax" data-ajax-url="_ajax/dummy_text.html" data-ajax-container="#section_1 .section-body" data-ajax-method="GET">

                <div class="section-body">
                    Lorem Ipsum
                </div>

                <a href="#" class="btn-js-ajax-reload" data-ajax-container="#section_1">Reload Content</a>

            </section>

            &ajax=true - added but NOT to .html files
     *  
     *
     **/
    ajax_content: function(_this) {

      if(_this.hasClass('.js-ajaxified') || _this.length < 1)
        return;

      _this.addClass('js-ajaxified');


      var _id                         = _this.attr('id')                          || '',
          _url                        = _this.data('ajax-url')                    || '',
          _callback                   = _this.data('ajax-callback-function')      || $.SOW.core.ajax_content.config.callback_function,
          _callbackBeforePush         = _this.data('ajax-callback-before-push')   || $.SOW.core.ajax_content.config.callback_before_push,
          _method                     = _this.data('ajax-method')                 || $.SOW.core.ajax_content.config.method,
          _btn_reload                 = _this.data('ajax-btn-reload')             || '',
          _target                     = _this.data('ajax-target-container')       || '';


      /* 
          
          Container has no ID
          We generate one

      */
      if(_id == '') {
        _id = 'rand_' + $.SOW.helper.randomStr(8);
        _this.attr('id', _id);
      }


      // Add a generated id to reload button, if has no id.
      // do not rewrite if exists
      if(jQuery(_btn_reload).length > 0) {

        var _btnReloadTarget = jQuery(_btn_reload).attr('data-ajax-container') || '';

        if(_btnReloadTarget == '')
          jQuery(_btn_reload).attr('data-ajax-container', '#'+_id).addClass('btn-js-ajax-content-reloader');

      }





      /* 

          No target specified
          We use self container

      */
      if(_target == '' || _target == 'self' || _target == '_self')
        var _target = '#' + _id;



      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
      // FUTURE UPDATE
      // PROECESS ONLY WHEN ELEMENT IS VISIBLE!
      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


      // Process!
      $.SOW.core.ajax_content.__ajaxDivProcess(_id, _url, _target, _method, null, _callback, _callbackBeforePush);
      // -- -- --


      // Bind reload button[s]
      $.SOW.core.ajax_content.btn_reload_bind();

    },






    /**
     *
     *  @btn_reload_bind
     *
     *  
     *
     **/
    btn_reload_bind: function() {

      jQuery('.btn-js-ajax-content-reloader:not(.btn-js-ajaxified)').addClass('btn-js-ajaxified').on('click', function(e) {
        e.preventDefault();

        var __btnEl         = jQuery(this),
        __containerID       = jQuery(this).data('ajax-container') || '';

        if(__containerID != '') {

          var __t                     = jQuery(__containerID),
              __id                    = __t.attr('id')                                || '',
              __url                   = __t.attr('data-ajax-url')                     || '', // .attr, ELSE WILL NOT UPDATE!
              __callback              = __t.data('ajax-callback-function')            || $.SOW.core.ajax_content.config.callback_function,
              __callbackBeforePush    = __t.data('ajax-callback-before-push')         || $.SOW.core.ajax_content.config.callback_before_push,
              __method                = __t.data('ajax-method')                       || $.SOW.core.ajax_content.config.method,
              __target                = __t.data('ajax-target-container')             || __containerID;

          // Process!
          $.SOW.core.ajax_content.__ajaxDivProcess(__id, __url, __target, __method, __btnEl, __callback, __callbackBeforePush);
          // -- -- --

        }

      });

    },







    /* 

        Function also called on reload ajax content!

        _btnEl - is reload button element
        Used only on reload content to disable|enable on ajax process

    */
    __ajaxDivProcess: function(_id, _url, _target, _method, _btnEl, _callback, _callbackBeforePush) {

      var _selector = this.selector;


      // Yeah, the ugliest bool fix ever.
      var _callbackBeforePush = _callbackBeforePush+'';
      var _callbackBeforePush = _callbackBeforePush.toLowerCase();


      jQuery.ajax({
          url:            _url,
          type:           _method,
          data:           $.SOW.core.ajax_content.config.data_params,
          contentType:    $.SOW.core.ajax_content.config.contentType,
          dataType:       $.SOW.core.ajax_content.config.dataType,
          headers:        $.SOW.core.ajax_content.config.headers,
          crossDomain:    $.SOW.core.ajax_content.config.crossDomain,

          beforeSend: function() {

            // We don't show loading icon on load!
            // Let user show it's own loading message
            // Show loading only for "reload button"
            if(_btnEl) {

              jQuery(_target).addClass('overlay-light overlay-opacity-6 overlay-over');
              $.SOW.helper.loadingSpinner('show', _target);
              _btnEl.addClass('disabled active').prop('disabled', true);

            }

          },

          error:  function(XMLHttpRequest, textStatus, errorThrown) {

            $.SOW.helper.loadingSpinner('hide');

            if(typeof $.SOW.core.toast === 'object') {
              $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!', 'bottom-center', 0, true);
            } else {
              alert("[404] Unexpected internal error!");
            }

            // if debug enabled, see config
            $.SOW.helper.consoleLog('----------------------------');
            $.SOW.helper.consoleLog('--- AJAX  REQUEST ERROR ----');
            $.SOW.helper.consoleLog('DIV|section ID: ' + _id);
            $.SOW.helper.consoleLog('1. XMLHttpRequest:');
            $.SOW.helper.consoleLog(XMLHttpRequest);
            $.SOW.helper.consoleLog('2. textStatus:');
            $.SOW.helper.consoleLog(textStatus);
            $.SOW.helper.consoleLog('3. errorThrown:');
            $.SOW.helper.consoleLog(errorThrown);
            $.SOW.helper.consoleLog('----------------------------');

          },

          success: function(data) {

            // Push data
            if(_callbackBeforePush == 'false')
              jQuery(_target).empty().html(data);


            // callback function
            if(_callback != '' && typeof $.SOW.helper.executeFunctionByName === "function")
              $.SOW.helper.executeFunctionByName(_callback, window, _id, _target, data);


            // notice
            if(_callbackBeforePush == 'true' && _callback == '')
              $.SOW.helper.consoleLog('data-ajax-callback-function="" -BUT- data-ajax-callback-before-push="true"');


            // enable reload button
            // if(_btnEl) {

              // Set a delay, so user know content loaded, if was too fast
              setTimeout(function() {

                $.SOW.helper.loadingSpinner('hide');
                jQuery(_target).removeClass('overlay-light overlay-opacity-6 overlay-over');

                if(_btnEl)
                  _btnEl.removeClass('disabled active').prop('disabled', false);


                // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
                if(_callbackBeforePush == 'false') {
                  // console log
                  $.SOW.helper.consoleLogReinit(scriptInfo, _target);
                  // reinit inside ajax container
                  $.SOW.reinit(_target);
                }
                // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

              }, 300);


            // }


          }

      });

      return true;

    },

  }

})(jQuery);
/**
 *
 *  [SOW] Ajax Form
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.ajax_form.init('form.js-ajax');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Ajax Form';




  $.SOW.core.ajax_form = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {

        method                          : 'GET',
        contentType                     : '',
        dataType                        : '',
        headers                         : '',
        crossDomain                     : '',

    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

        var __selector          = $.SOW.helper.__selector(selector);
        var __config            = $.SOW.helper.check_var(config);

        this.selector           = __selector[0];    // '#selector'
        this.collection         = __selector[1];    // $('#selector')
        this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;



        if(jQuery(this.selector).length < 1)
            return;


        // -- * --
        $.SOW.helper.consoleLog('Init : ' + scriptInfo);
        // -- * --


        // 1. Has no selector
        if(!this.selector) {
            $.SOW.core.ajax_form.process($('form.js-ajax'));
            return this.collection;
        }

        // 2. Has selector
        return this.collection.each(function() {
            
            $.SOW.core.ajax_form.process($(this));

        });

    },



    /**
     *
     *  @ajax_form
     *
        3. FORMS


            EXAMPLE USAGE.  (form.js-ajax  - REQUIRED)

                <form class="js-ajax bs-validate" novalidate action="YOUR_URL" data-ajax-container="#ajax_container" data-ajax-update-url="true">

                    <input required type="text" name="first_name" value="" placeholder="first name" class="form-control mb-4">
                    <input type="text" name="birthdate" value="" placeholder="birthdate" class="form-control mb-4">

                    <!-- 
                        Error Notice - more visible

                        Instead of this alert, you can use a toast alert instead by adding to form:
                            data-error-toast-text="<i class='fi fi-circle-spin fi-spin float-start'></i> Please, complete all required fields!" 
                            data-error-toast-delay="3000" 
                            data-error-toast-position="top-right"

                    -->
                    <div class="bs-validate-info hide alert alert-danger" data-error-alert-delay="4000">
                        <i class="fi fi-circle-spin fi-spin float-start"></i> 
                        Please, complete all required fields!
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                

                <!-- position relative|fixed|absolute is required for loading icon -->
                <div id="ajax_container" class="position-relative w--500 pl--30 pr--30 pt--80 pb--80">
                    <!-- RESPONSE ADDED HERE, IF NEEDED -->
                </div>


            ajax-callback-function=""   - used to handle the response, instead of appending the response to a container
     *  
     *
     **/
    process: function(_this) {

        var _selector       = this.selector;
        var _selector_orig  = this.selector_orig;

        _this.submit(function(e) {

            var _t                  = _this,
                _formID             = _t.attr('id')                             || '',
                _action             = _t.attr('action')                         || '',
                _callback           = _t.attr('data-ajax-callback-function')    || '',
                _method             = _t.attr('method')                         || $.SOW.core.ajax_form.config.method,
                _updateURL          = _t.attr('data-ajax-update-url')           || 'false',

                appendData          = _t.attr('data-ajax-append-response')      || '',

                _scrollErrUp        = _t.attr('data-error-scroll-up')           || 'false',
                _showLoadingIcon    = _t.attr('data-ajax-show-loading-icon')    || 'true',
                _contentType        = _t.data('ajax-contentType')               || '',
                _dataType           = _t.data('ajax-dataType')                  || '',
                _target             = _t.data('ajax-container')                 || '',
                _toast_text         = _t.data('error-toast-text')               || '',          // toast alert for .bs-validate
                _toast_delay        = _t.data('error-toast-delay')              || 0,           // toast alert for .bs-validate
                _toast_pos          = _t.data('error-toast-position')           || "top-right", // toast alert for .bs-validate
                _toast_success      = _t.data('success-toast-text')             || "",
                
                _controlAlerts      = _t.attr('data-ajax-control-alerts')               || "false",
                _controlSuccess     = _t.attr('data-ajax-control-alert-succes')         || "",
                _controlUnexpected  = _t.attr('data-ajax-control-alert-unexpected')     || "",
                _controlMandatory   = _t.attr('data-ajax-control-alert-mandaroty')      || "",
                
                // show|hide container for error|success
                _onSucessShow       = _t.data('ajax-inline-alert-succes')       || '',
                _onErrorShow        = _t.data('ajax-inline-alert-error')        || '',

                // autoclose modal on success
                _modalCloseOnSuccess        = _t.data('modal-autoclose-on-success')         || 'false',
                _modalCloseOnSuccessDelay   = _t.data('modal-autoclose-on-success-delay')   || 0;

            if(_onSucessShow != '' || _onErrorShow != '') {
                if(_target == '#middle')
                    _target = '';
            }

            // the most uglies bool hack
            var _scrollErrUp        = _scrollErrUp+'';
            var _updateURL          = _updateURL+'';

            // Assign a random id if not exist
            if(_formID == '') {
                var _formID = 'js_' + $.SOW.helper.randomStr(10);
                _t.attr('id', _formID);
            }

            // Bootstrap Validation +++++++++++++++++++++++++++++++++++++++++++++++++++
            // This part already exists in _formvalidate.js
            // We use it again here in case the file is not loaded!

            // default reset
            window.ajax_form_stop = false;

            // Form Element
            var _form = document.getElementById(_formID);

            if(_t.hasClass('bs-validate')) {

                // hide all errors info
                jQuery('.bs-validate-info').addClass('hide hide-force');

                if(typeof $.SOW.core.toast === 'object')
                    $.SOW.core.toast.destroy();

                if(_form.checkValidity() === false) {


                    // -- message|toast ---
                    if(_toast_text != '') {

                        if(typeof $.SOW.core.toast === 'object') {
                            
                            if(Number(_toast_delay) < 1)
                                var _toast_delay = 4000;

                            $.SOW.core.toast.show('danger', '', _toast_text, _toast_pos, Number(_toast_delay), true);

                        } else {

                            alert(_toast_text);

                        }

                    } else {

                        // show error info
                        jQuery('.bs-validate-info', _t).removeClass('hide hide-force'); // show error info
                        
                        // error info delay timeout
                        var _delay = jQuery('.bs-validate-info', _t).data('error-alert-delay') || 3000;

                        // hide error info in X seconds
                        setTimeout(function() {
                            jQuery('.bs-validate-info', _t).addClass('hide hide-force'); 
                        }, Number(_delay));

                    }
                    // -- -- -- --

                    // show|hide custom containers
                    if(_onErrorShow != '') {
                        jQuery(_onSucessShow).addClass('hide hide-force');
                        jQuery(_onErrorShow).removeClass('hide hide-force');
                    }
                    
                    // Focus invalid element and scroll
                    jQuery('.form-control:invalid', _t).first().focus();

                    // Do not animate inside modal!
                    if(typeof $.SOW.helper.scrollAnimate === "function" && _scrollErrUp == 'true' && !jQuery('.modal').hasClass('show')) {
                        var _formEl = jQuery('input:invalid, select:invalid, textarea:invalid', _t);
                        $.SOW.helper.scrollAnimate(_formEl, 0, false, 200);
                    }
                    // -- -- -- --

                    e.preventDefault();
                    e.stopPropagation();
                    window.ajax_form_stop = true;
                } 


                _t.addClass('was-validated');


                // Stop here!
                if(window.ajax_form_stop === true)
                    return;

            } else {

                if(_form.checkValidity() === false) {

                    e.preventDefault();
                    e.stopPropagation();
                    window.ajax_form_stop = true;
                } 

            }

            // Stop here!
            if(window.ajax_form_stop === true)
                return;

            // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


            // Start the show
            e.preventDefault();

            if(_action == '') {

                $.SOW.helper.consoleLog('Ajax request: form action missing!');

                if(typeof $.SOW.core.toast === 'object') {

                    $.SOW.core.toast.show('danger', 'Error', 'Unexpected Internal error!', 'bottom-center', 0, true);

                } else {

                    alert("[404] Unexpected internal error!");

                }

                return;
            }


            if(_method.toLowerCase() == 'post') {

                var formData        = new FormData(this);
                var __processData   = false;
                var __contentType   = false;

            } else {

                var __processData   = true;
                var __contentType   = _contentType || $.SOW.core.ajax_form.config.contentType;
                var formData        = _t.serializeArray();

            }


            jQuery.ajax({
                url:            _action,
                data:           formData,
                type:           _method,
                dataType:       _dataType || $.SOW.core.ajax_form.config.dataType,
                headers:        $.SOW.core.ajax_form.config.headers,
                crossDomain:    $.SOW.core.ajax_form.config.crossDomain,
                contentType:    __contentType,
                processData:    __processData,
                cache:          false,

                beforeSend: function() {

                    // icon over form
                    if(_showLoadingIcon == 'true')
                        $.SOW.helper.loadingSpinner('show', _t);

                    // Disable submit button
                    jQuery(this).attr('disabled', true).addClass('disabled');

                },

                error:  function(XMLHttpRequest, textStatus, errorThrown) {

                    $.SOW.helper.loadingSpinner('hide');

                    if(typeof $.SOW.core.toast === 'object') {

                        $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!', _toast_pos, 0, true);

                    } else {

                        alert("[404] Unexpected internal error!");

                    }

                    // show|hide custom containers
                    if(_onErrorShow != '') {
                        jQuery(_onSucessShow).addClass('hide hide-force');
                        jQuery(_onErrorShow).removeClass('hide hide-force');
                    }

                    // Enable submit button
                    jQuery(this).attr('disabled', false).removeClass('disabled');
                },

                success: function(data) {

                    $.SOW.helper.loadingSpinner('hide');
                    $.SOW.helper.consoleLog(data);

                    // Enable submit button
                    jQuery(this).attr('disabled', false).removeClass('disabled');
                    var hasErrors = false;

                    // Control alerts
                    if(_controlAlerts+'' == 'true') {

                        // reset first
                        var hasErrors = true;
                        jQuery(_controlSuccess).addClass('hide hide-force');
                        jQuery(_controlUnexpected).addClass('hide hide-force');
                        jQuery(_controlMandatory).addClass('hide hide-force');

                        if(data == '{:success:}') {
                            jQuery(_controlSuccess).removeClass('hide hide-force');
                            var hasErrors = false;
                            var data = '';
                        }

                        else if(data == '{:err:required:}')
                            jQuery(_controlMandatory).removeClass('hide hide-force');

                        else if(data == '{:err:unexpected:}')
                            jQuery(_controlUnexpected).removeClass('hide hide-force');


                        if(hasErrors === true) {
                            
                            // this is a server message - debug only!
                            if($.SOW.config.sow__debug_enable === true) 
                                jQuery(_target).empty().html(data);
                            else
                                jQuery(_target).empty().html('Server Error!');
                                

                            _t.removeClass('was-validated');
                            return;
                        }
                        
                    }



                    // Callbat and/or Reset
                    if(_callback == '') {

                        // Reset Form
                        jQuery('input:not([type=hidden])', _t).val('');
                        jQuery('textarea:not(.hide)', _t).val('');

                        if(_target != '')
                            jQuery(_target).empty().html(data);

                        // reset form validation
                        _t.removeClass('was-validated');

                    } else {

                        if(typeof $.SOW.helper.executeFunctionByName === "function")
                            $.SOW.helper.executeFunctionByName(_callback, window, _t, data);

                    }



                    // Update URL
                    if(_updateURL == 'true' && $.SOW.core.ajax_navigation === 'object')
                        $.SOW.core.ajax_navigation.__historyPushState(_action, '', data);


                    if(_target != '' && typeof $.SOW.helper.scrollAnimate === "function" && _scrollErrUp == 'true') {
                        var _elResponse = jQuery(_target || _onSucessShow);
                        if(_elResponse.length)
                            $.SOW.helper.scrollAnimate(_elResponse, 0, false, 200);
                    }


                    // show|hide custom containers
                    if(_onSucessShow != '') {
                        jQuery(_onSucessShow).removeClass('hide hide-force');
                        jQuery(_onErrorShow).addClass('hide hide-force');
                    }



                    // if form is on modal
                    if(_modalCloseOnSuccess+'' == 'true') {

                        setTimeout(function() {
                            jQuery('#sow_ajax_modal').modal('hide');
                        }, Number(_modalCloseOnSuccessDelay));
                        
                    }


                    // success tost, if text provided
                    if(typeof $.SOW.core.toast === 'object' && _toast_success != '')
                        $.SOW.core.toast.show('success', '', _toast_success, _toast_pos, 1500, true);


                    // Reload Container via Ajax
                    if(appendData != '') {
                        
                        setTimeout(function() {

                            jQuery(appendData).empty().append(data);

                            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
                            // console log
                            $.SOW.helper.consoleLogReinit(scriptInfo, appendData);
                            // reinit inside ajax container
                            $.SOW.reinit(appendData);
                            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

                        }, (Number(_modalCloseOnSuccessDelay) + 100 ) );

                    }

                }

            });


        });


    }

  }

})(jQuery);

/**
 *
 *  [SOW] Ajax Modal
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.ajax_modal.init('.js-ajax-modal');
 * 
    // Programtically Create Modal  :             url , 'modal-md', 'true' (centered), 'backdrop' (optional), callback (optional)
    $.SOW.core.ajax_modal.createFromThinAir(modalUrl, 'modal-lg', 'true', 'static', callback);

    // Programtically Attach Element/Link ( $('.selector') or '.selector' )
    $.SOW.core.ajax_modal.attach(selector);

 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Ajax Modal';



  $.SOW.core.ajax_modal = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {
      method                          : 'GET',
      contentType                     : '',   // jQuery params
      dataType                        : '',   // jQuery params
      headers                         : '',   // jQuery params
      crossDomain                     : '',   // jQuery params
      data_params                     : {ajax:'true'},

      modal_container                 : '#sow_ajax_modal',
      modal_size                      : 'modal-md',
      modal_centered                  : 'false',

      /* 
          callback_example = function(el, data, modal_container) {
              // el               = element               $(this)
              // data             = server response           (html|string)
              // modal_container = container to push data     (string:#middle)
          }
      */
      callback_function   : ''
    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.selector_orig      = __selector[2];    // $('#selector') // without ajax container prefix
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      if(jQuery(this.selector).length < 1)
          return;


      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      // Create modal container
      $.SOW.core.ajax_modal.ajax_modal_template();



      // 1. Has no selector
      if(!this.selector) {
        $.SOW.core.ajax_modal.ajax_modal($('.js-ajax-modal'));
        return $('.js-ajax-modal');
      }

      // 2. Has selector
      return this.collection.each(function() {
          
        $.SOW.core.ajax_modal.ajax_modal($(this));

      });

    },



    /**
     *
     *  @ajax_modal
     *
     *  
     *
     **/
    ajax_modal: function(_this) {

      var _selector           = this.selector,
          _selector_orig      = this.selector_orig,
          _modal_container    = this.config.modal_container,
          _modal_size         = this.config.modal_size,
          _callback           = this.config.callback_function,
          _method             = this.config.method,
          _modal_centered     = this.config.modal_centered;

      // precech to avoid useless bind. checked again on click!
      var _href               = _this.attr('href')    || '',
          _href_data          = _this.data('href')    || '';

      if(_href_data != '') _href = _href_data;
      if(_href == '') return;


      _this.not('.js-modalified').addClass('js-modalified').on('click', function(e) {
          e.preventDefault();

          var _t                  = jQuery(this),
              _href               = _t.attr('href')                           || '',
              _href_data          = _t.data('href')                           || '',
              _modalType          = _t.attr('data-ajax-modal-type')           || '',              // video (or more in the future)
              _target             = _t.data('ajax-modal-container')           || _modal_container,
              _modal_callback     = _t.data('ajax-modal-callback-function')   || _callback,       // callback function to call, on modal load
              _modal_size         = _t.data('ajax-modal-size')                || _modal_size,     // modal-sm, modal-md, modal-lg , modal-full
              _modal_backgdrop    = _t.data('ajax-modal-backdrop')            || '',              // data-backdrop="static"   -  do not close on click
              _modal_centered     = _t.data('ajax-modal-centered')            || _modal_centered, // true|false
              _method             = _t.data('ajax-modal-method')              || _method;         // true|false

          if(_href_data != '') _href = _href_data;
          if(_href == '' || _href == '#' || _href == 'javascript:;') return;
          if(_t.hasClass('js-ignore')) return; // Ignore by request!


          /* 
              Empty ajax modal on close
              else, plugins like selectpicker will not refresh 
          */  jQuery(_target + ' .modal-content').empty();


          // Close any open dropdown
          jQuery('.dropdown-menu:not(.dropdown-menu-hover)').parent().find('a[data-bs-toggle="dropdown"][aria-expanded="true"]').attr('aria-expanded', 'false').dropdown('hide');

          // Call the trigger
          $.SOW.core.ajax_modal.modalAjaxShow(_href, _target, _modal_size, _modal_centered, _modal_backgdrop, _modal_callback, _method, _modalType);

      }); 


      // Modals on load
      $.SOW.core.ajax_modal.ajax_modal_onLoad();

    },

      /**
       *
       *  Ajax Process
       *  :: Helper
       *
       **/
      modalAjaxShow: function(_href, _target, _modal_size, _modal_centered, _modal_backgdrop, _modal_callback, _method, _modalType) {

        var _modal_size         = typeof _modal_size        !== 'undefined' ? _modal_size       : this.config.modal_size;
        var _modal_centered     = typeof _modal_centered    !== 'undefined' ? _modal_centered   : this.config.modal_centered;
        var _modal_backgdrop    = typeof _modal_backgdrop   !== 'undefined' ? _modal_backgdrop  : '';
        var _modal_callback     = typeof _modal_callback    !== 'undefined' ? _modal_callback   : '';


        // Create modal container (if not exists)
        $.SOW.core.ajax_modal.ajax_modal_template();


        if(_href == '#' || _href == '') return;
        if(_target == '')               var _target = this.config.modal_container;
        if(_modal_size == '')           var _modal_size = this.config.modal_size;  // modal-lg , modal-full

        // --

        // Remove any known size class
        jQuery('.modal-dialog', _target).removeClass('modal-dialog-centered modal-sm modal-lg modal-xl modal-full');

        // Add size class by request
        if(_modal_size != '')
          jQuery('.modal-dialog', _target).addClass(_modal_size);

        // centered
        if(_modal_centered == true)
          jQuery('.modal-dialog', _target).addClass('modal-dialog-centered');

        // static, do not close on click
        if(_modal_backgdrop != '') {
          var _backdrop = 'static';   
          var _keyboard = false;
        } else {
          var _backdrop = 'dynamic';  
          var _keyboard = true;
        }

        // RESET - needed to load a modal from another modal
        jQuery('.modal-backdrop').remove();


        // ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++
        // is video modal! stop here! process video!
        if(_modalType.toLowerCase() +'' == 'video') {

          var _backdrop = 'dynamic';  // forced
          var embedData = $.SOW.helper.videoEmbedFromUrl(_href, 1); // 1 = autoplay

          if(embedData === null) {

            if(typeof $.SOW.core.toast === 'object') {
                $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!', 'bottom-center', 0, true);
            } else {
                alert("[404] Unexpected internal error!");
            }

            return false;

          }




          // load modal
          var _loader = '<div class="text-center p-6 fs-1 text-muted '+$.SOW.config.sow__icon_loading+'"></div>';
          

          // BS4
          // -- -- --
          // jQuery(_target).modal('dispose').modal({backdrop: _backdrop, keyboard:_keyboard}).find('.modal-content').html(_loader);
          // -- -- --


          // BS5
          // -- -- --
          var ajxModal = document.querySelector(_target);
              ajxModal.querySelector('.modal-content').innerHTML = _loader; // reset & show loading spinner

          var myModal = new bootstrap.Modal(ajxModal, {
            backdrop: _backdrop, 
            keyboard:_keyboard
          }); myModal.show();
          // -- -- --


          setTimeout(function () {

            jQuery(_target).find('.modal-content')
                            .html(embedData)
                            .addClass('rounded-xl bg-dark shadow-primary-xs border border-dark border-3')
                            .prepend('<button type="button" style="margin-top:-18px;margin-right:-15px;width:34px;height:34px;" class="border-0 d-flex align-items-center justify-content-center pointer position-absolute top-0 end-0 text-white bg-dark rounded-circle z-index-100" data-bs-dismiss="modal" aria-label="Close"><span class="fi fi-close fs-6" aria-hidden="true"></span></button>');


            // try another way to start the video! AdBlock is problematic!
            jQuery('iframe', _target).attr('src', jQuery('iframe', _target).data('autoplay-src')).addClass('rounded-xl');

          }, 450);


          // Destroy on close! Too much customization!
          // And anyway, else, video remain to play on background!
          jQuery(_target).on('hidden.bs.modal', function (e) {
            jQuery(_target).remove();
            document.body.removeAttribute('data-bs-overflow');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
          });
          // document.body.removeAttribute('data-bs-overflow');
          // ajxModal.addEventListener('hidden.bs.modal', function (event) {
          //   setTimeout(function() {
          //     if( ajxModal ) ajxModal.parentElement.removeChild( ajxModal );
          //   }, 50);
          // })



          return false;

        }
        // ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++



        // Ajax Request
        jQuery.ajax({
          url:            _href,
          type:           _method,
          data:           $.SOW.core.ajax_modal.config.data_params,
          contentType:    $.SOW.core.ajax_modal.config.contentType,
          dataType:       $.SOW.core.ajax_modal.config.dataType,
          headers:        $.SOW.core.ajax_modal.config.headers,
          crossDomain:    $.SOW.core.ajax_modal.config.crossDomain,

          beforeSend: function() {

            var _loader = '<div class="text-center p-6 fs--30 text-muted '+$.SOW.config.sow__icon_loading+'"></div>';


            // BS4
            // -- -- --
            // jQuery(_target).modal('dispose').modal({backdrop: _backdrop, keyboard:_keyboard}).find('.modal-content').html(_loader);
            // -- -- --


            // BS5
            // -- -- --
            var ajxModal = document.querySelector(_target);
                ajxModal.querySelector('.modal-content').innerHTML = _loader; // reset & show loading spinner

            var myModal = new bootstrap.Modal(ajxModal, {
              backdrop: _backdrop, 
              keyboard:_keyboard
            }); myModal.show();
            // -- -- --


          },

          error:  function(XMLHttpRequest, textStatus, errorThrown) {

            if(typeof $.SOW.core.toast === 'object') {
              $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!', 'bottom-center', 0, true);
            } else {
              alert("[404] Unexpected internal error!");
            }

          },

          success: function(data) {

            jQuery(_target).find('.modal-content').html(data);

            setTimeout(function () {

              // Optional callback function
              if(_modal_callback != '' && typeof $.SOW.helper.executeFunctionByName === 'function')
                $.SOW.helper.executeFunctionByName(_modal_callback, window, _target);

              // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
              // console log
              $.SOW.helper.consoleLogReinit(scriptInfo, _target);
              // reinit inside ajax container
              $.SOW.reinit(_target);
              // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
                  
              // focus first input element
              jQuery('input:visible:enabled:first', _target).focus();


            }, 450);

          }
        });


      },





    /**
     *
     *  @ajax_modal_template
     *
     *
     **/
    ajax_modal_template: function() {

      if(jQuery(this.config.modal_container).length > 0)
        return;

      var modal_container = this.config.modal_container.replace('#', ''),
          _tpl = '<div class="modal fade" id="' + modal_container + '" role="dialog" tabindex="-1" aria-hidden="true">'
                + '<div class="modal-dialog '+ this.config.modal_size +'" role="document">'

                  + '<div class="modal-content"><!-- content added by ajax --></div>'

                + '</div>'
              + '</div>';

      $.SOW.globals.elBody.append(_tpl);
      $(this.config.modal_container).modal('handleUpdate');

    },





    /**
     *
     *  @createFromThinAir
     * Programtically Create Modal  :             url , 'modal-md', 'true' (centered), 'backdrop' (optional), callback (optional)
     * $.SOW.core.ajax_modal.createFromThinAir(modalUrl, 'modal-lg', 'true', 'static', callback);
     *
     **/
    createFromThinAir: function(url, modal_size, modal_centered, modal_backdrop, modal_callback) {

      if(!url) $.SOW.helper.consoleLog('SOW Ajax Modal : [createFromThinAir()] : No url provided!');

      if(!this.selector_orig) this.selector_orig = '.js-ajax-modal';
      if(!modal_size)         modal_size = 'modal-md';
      if(!modal_centered)     modal_centered = 'false';
      if(!modal_backdrop)     modal_backdrop = '';
      if(!modal_callback)     modal_callback = '';

      // Create DOM
      var selectorClass = this.selector_orig.replace('.', '');
      $.SOW.globals.elBody.append('<a id="ajax_modal_create_tmp" href="'+url+'" class="hide '+selectorClass+'"></a>');

      // Add Attributes
      jQuery('#ajax_modal_create_tmp')
        .attr('data-ajax-modal-size',           modal_size)
        .attr('data-ajax-modal-centered',       modal_centered)
        .attr('data-ajax-modal-backdrop',       modal_backdrop)
        .attr('data-ajax-callback-function',    modal_callback);

      // Init this plugin
      $.SOW.core.ajax_modal.init(this.selector_orig);
      jQuery('#ajax_modal_create_tmp').trigger('click');

      // Delete, not needed anymore!
      setTimeout(function() {
        jQuery('#ajax_modal_create_tmp').off().remove();
      },350);

      return true;

    },






    /**
     *
     *  @ajax_modal_onLoad
     *
     **/
    ajax_modal_onLoad: function() {

      jQuery('.js-ajax-modal.js-onload'+this.selector).not('.js-loadmodalified').addClass('js-loadmodalified').each(function() {

        var _t          = jQuery(this),
            _delay      = jQuery(this).attr('data-ajax-modal-delay') || 3000,
            _ID         = _t.attr('id') || '';

        if(_ID != '') {
          var modalCookie = Cookies.get(_ID, { path: '/' });
          if(modalCookie == '1') return;
        }

        setTimeout(function() {
          _t.trigger('click');
        }, Number(_delay));

      });

    },





    /**
     *
     *  @attach
     *  Programtically Init For Element
     *  $.SOW.core.ajax_modal.attach(bg_element|string_id_class);
     *
     **/
    attach: function(href, delay) {

      // obj required
      var el = (typeof href === 'string')     ? jquery(href)  : href;
      var dl = (typeof delay === 'number')    ? delay         : 200;

      // unbind
      if(el.hasClass('js-modalified'))
        el.off().removeClass('js-modalified');

      // add required
      el.addClass('js-ajax-modal');

      // init
      $.SOW.core.ajax_modal.ajax_modal(el);
      setTimeout(function() {
        el.trigger('click');
      },dl);

      return true;

    },




    /**
     *
     *  @Return Selector
     *
     *
     **/
    __selector: function() {
      return this.selector_orig;
    }


  }

})(jQuery);
/**
 *
 *  [SOW] Ajax Confirm
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.ajax_confirm.init('.js-ajax-confirm');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Ajax Confirm';



  $.SOW.core.ajax_confirm = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {

      method                          : 'GET',    // (ajax mode only)
      contentType                     : '',       // jQuery params (ajax mode only)
      dataType                        : '',       // jQuery params (ajax mode only)
      headers                         : '',       // jQuery params (ajax mode only)
      crossDomain                     : '',       // jQuery params (ajax mode only)
      data_params                     : {ajax:'true'},// jQuery params (ajax mode only)

      confirm_container               : '#sow_ajax_confirm',
      confirm_size                    : '',
      confirm_centered                : false,

      confirm_type                    : '',           // primary|secondary|success|warning|danger|info; empty = regular/clean
      confirm_mode                    : 'regular',    // ajax|regular
      confirm_title                   : 'Please Confirm',
      confirm_body                    : 'Are you sure?',

      btn_class_yes                   : 'btn-sm btn-primary',
      btn_class_no                    : 'btn-sm btn-light',
      btn_text_yes                    : 'Confirm',
      btn_text_no                     : 'Cancel',
      btn_icon_yes                    : 'fi fi-check',
      btn_icon_no                     : 'fi fi-close',

      /* 
          callback_example = function(el, data) {
              // el               = link clicked              $(this)
              // data             = server response           (html|string)
          }
      */
      callback_function               : ''

    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.selector_orig      = __selector[2];    // $('#selector') // without ajax container prefix
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      if(jQuery(this.selector).length < 1)
        return;


      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      // Create modal container
      $.SOW.core.ajax_confirm.ajax_confirm_template();


      // 1. Has no selector
      if(!this.selector) {
        $.SOW.core.ajax_confirm.ajax_confirm($('a.js-ajax-confirm'));
        return this.collection;
      }

      // 2. Has selector
      return this.collection.each(function() {
          
        $.SOW.core.ajax_confirm.ajax_confirm($(this));

      });

    },



    /**
     *
     *  @ajax_confirm
     *

    <a href="URL-HERE" 
        class="js-ajax-confirm"

        data-ajax-confirm-size="modal-md" 
        data-ajax-confirm-centered="false" 

        data-ajax-confirm-callback-function=""
        data-ajax-confirm-type="ajax" 
        data-ajax-confirm-method="GET" 

        data-ajax-confirm-title="Please Confirm" 
        data-ajax-confirm-body="Are you sure? Delete this item?" 

        data-ajax-confirm-btn-yes-text="Confirm" 
        data-ajax-confirm-btn-yes-class="btn-sm btn-primary" 
        data-ajax-confirm-btn-yes-icon="fi fi-check" 

        data-ajax-confirm-btn-no-text="Cancel" 
        data-ajax-confirm-btn-no-class="btn-sm btn-light" 
        data-ajax-confirm-btn-no-icon="fi fi-close"

        rel="nofollow">
        Confirm
    </a>
        
        data-ajax-confirm-size="modal-sm|modal-md|modal-lg|modal-full"

        data-ajax-confirm-callback-function="_myCustomFunc"     (optional - your custom function called on load)
        Example: 
            var _myCustomFunc = function(el, data) {
                el = button element
                data = ajax result from server
                alert('My Function Called! Do something like... reinit some plugins');
            }

     *  
     *
     **/
    ajax_confirm: function(_this) {

      var _selector           = this.selector,
          _selector_orig      = this.selector_orig,
          _confirm_container  = this.config.confirm_container,
          _confirm_size       = this.config.confirm_size,
          _confirm_centered   = this.config.confirm_centered,
          _confirm_callback   = this.config.callback_function,

          confirm_method      = this.config.method,
          confirm_mode        = this.config.confirm_mode,
          confirm_type        = this.config.confirm_type,
          confirm_title       = this.config.confirm_title,
          confirm_body        = this.config.confirm_body,

          btn_class_yes       = this.config.btn_class_yes,
          btn_class_no        = this.config.btn_class_no,
          btn_text_yes        = this.config.btn_text_yes,
          btn_text_no         = this.config.btn_text_no,
          btn_icon_yes        = this.config.btn_icon_yes,
          btn_icon_no         = this.config.btn_icon_no;


      var _href       = _this.attr('href') || '',
          _href_data  = _this.data('href') || '';

      if(_href_data != '') _href = _href_data;
      if(_href == '') return;

      _this.attr('data-href', _href);
      _this.attr('href', '#');

      _this.not('.js-ajaxconfirmified').addClass('js-ajaxconfirmified').on('click', function(e) {
          e.preventDefault();

          var _t                  = jQuery(this),
              _href               = _t.data('href')                           || '#',

              _target             = _t.data('ajax-confirm-container')         || _confirm_container,  // modal (id or class)
              _modal_confirm_size = _t.data('ajax-confirm-size')              || _confirm_size,       // modal-sm, modal-md, modal-lg , modal-full
              _modal_centered     = _t.data('ajax-confirm-centered')          || _confirm_centered,   // true|false
              _confirmCallback    = _t.data('ajax-confirm-callback-function') || _confirm_callback,   // custom function
              _confirmType        = _t.data('ajax-confirm-type')              || confirm_type,        // confirmation type: danger|warning|etc. empty for normal/clean
              _confirmMode        = _t.data('ajax-confirm-mode')              || confirm_mode,        // confirmation type: regular|ajax
              _confirmMethod      = _t.data('ajax-confirm-method')            || confirm_method,      // confirmation method: GET|POST
              
              _confirmSuccessEl       = _t.data('ajax-confirm-success-target')        || '',                  // element
              _confirmSuccessElAction = _t.data('ajax-confirm-success-target-action') || '',                  // action: remove|addClass|removeClass|toggleClass
              _confirmSuccessElClass  = _t.data('ajax-confirm-success-target-class')  || '',                  // class to remove or add

              _confirmTitle       = _t.data('ajax-confirm-title')             || confirm_title,       // modal title
              _confirmBody        = _t.data('ajax-confirm-body')              || confirm_body,        // message | question
              
              _confirmBtnYesTxt   = _t.data('ajax-confirm-btn-yes-text')      || btn_text_yes,        // button text
              _confirmBtnYesClass = _t.data('ajax-confirm-btn-yes-class')     || btn_class_yes,       // button class
              _confirmBtnYesIcon  = _t.data('ajax-confirm-btn-yes-icon')      || btn_icon_yes,        // button icon. eg: fi fi-check

              _confirmBtnNoTxt    = _t.data('ajax-confirm-btn-no-text')       || btn_text_no,         // button text
              _confirmBtnNoClass  = _t.data('ajax-confirm-btn-no-class')      || btn_class_no,        // button class
              _confirmBtnNoIcon   = _t.data('ajax-confirm-btn-no-icon')       || btn_icon_no;         // button icon. eg: fi fi-check
              


          // Ignore by request!
          if(_t.hasClass('js-ignore'))
              return;


          // Always create new modal
          $.SOW.core.ajax_confirm.ajax_confirm_template();

          // Close any open dropdown
          jQuery('.dropdown-menu:not(.dropdown-menu-hover)').parent().find('a[data-bs-toggle="dropdown"][aria-expanded="true"]').attr('aria-expanded', 'false').dropdown('hide');


          // Add size class by request
          jQuery('.modal-dialog', _target).removeClass('modal-sm, modal-md, modal-lg, modal-xlg, modal-full bg-primary-soft bg-secondary-soft bg-success-soft bg-warning-soft bg-danger-soft bg-pink-soft bg-indigo-soft bg-purple-soft').addClass(_modal_confirm_size);


          // Header Bg Color
          jQuery('.modal-header', _target).removeClass('bg-primary-soft bg-secondary-soft bg-success-soft bg-warning-soft bg-danger-soft bg-info-soft bg-pink-soft bg-indigo-soft bg-purple-soft');
          if(_confirmType != '')
              jQuery('.modal-header', _target).addClass('bg-'+_confirmType+'-soft');


          // centered
          (_modal_centered+'' == 'true') ? jQuery('.modal-dialog', _target).addClass('modal-dialog-centered') : jQuery('.modal-dialog', _target).removeClass('modal-dialog-centered');


          // Reset
          jQuery('.btn-confirm-yes', _target).attr('class', '').attr('class', 'btn btn-confirm-yes');
          jQuery('.btn-confirm-no', _target).attr('class', '').attr('class', 'btn btn-confirm-no');
          jQuery('.btn-confirm-yes>i', _target).remove();
          jQuery('.btn-confirm-no>i', _target).remove();
          jQuery(_target + ' .modal-footer').removeClass('hide');


          // Add Icons
          if(_confirmBtnYesIcon.length > 1)
              var _confirmBtnYesTxt = '<i class="' + _confirmBtnYesIcon + '"></i> ' + _confirmBtnYesTxt;

          if(_confirmBtnNoIcon.length > 1)
              var _confirmBtnNoTxt = '<i class="' + _confirmBtnNoIcon + '"></i> ' + _confirmBtnNoTxt;

          jQuery('.btn-confirm-yes', _target).attr('href', _href).html(_confirmBtnYesTxt).addClass(_confirmBtnYesClass);
          jQuery('.btn-confirm-no', _target).html(_confirmBtnNoTxt).addClass(_confirmBtnNoClass);

          jQuery('.modal-title', _target).html(_confirmTitle);
          jQuery('.modal-body', _target).html(_confirmBody);

          jQuery(_target).modal('show');

          // CONFIRMATION AJAX REQUEST
          if(_confirmMode == 'ajax') {

              jQuery('.btn-confirm-yes', _target).unbind('click').on("click", function(e) {
                  e.preventDefault();

                  jQuery.ajax({
                      url:            _href,
                      type:           _confirmMethod,
                      data:           $.SOW.core.ajax_confirm.config.data_params,
                      contentType:    $.SOW.core.ajax_confirm.config.contentType,
                      dataType:       $.SOW.core.ajax_confirm.config.dataType,
                      headers:        $.SOW.core.ajax_confirm.config.headers,
                      crossDomain:    $.SOW.core.ajax_confirm.config.crossDomain,

                      beforeSend: function() {

                          $.SOW.helper.loadingSpinner('show', _target + ' .modal-content');
                          jQuery('.btn-confirm-yes', _target).addClass('disabled').prop('disabled', true);

                      },

                      error:  function(XMLHttpRequest, textStatus, errorThrown) {

                          $.SOW.helper.loadingSpinner('hide');
                          jQuery('.btn-confirm-yes', _target).removeClass('disabled').prop('disabled', false);

                          if(typeof $.SOW.core.toast === 'object') {

                              $.SOW.core.toast.show('danger', '404 Error', 'Unexpected Internal error!',  'top-center', 0, true);
                              jQuery(_target).modal('hide');

                          } else {
                              alert("[404] Unexpected internal error!");
                          }

                      },

                      success: function(data) {

                          $.SOW.helper.loadingSpinner('hide');

                          jQuery(_target + ' .modal-footer').addClass('hide');
                          jQuery(_target + ' .modal-content .modal-body').html('<div class="js-modal-confirm-ok py-5 fs-1 text-center animate-bouncein"><i class="'+$.SOW.config.sow__icon_check+' text-muted"></i></div>');
                          jQuery('.btn-confirm-yes', _target).removeClass('disabled').prop('disabled', false);

                          setTimeout(function(){

                              jQuery(_target).modal('hide');

                          },500);
                          

                          if(_confirmCallback != '' && typeof $.SOW.helper.executeFunctionByName === 'function')
                              $.SOW.helper.executeFunctionByName(_confirmCallback, window, _t, data);


                          // Success: actions
                          if(_confirmSuccessEl != '') {

                              if(_confirmSuccessElAction == 'remove')
                                  jQuery(_confirmSuccessEl).remove();

                              else if(_confirmSuccessElAction == 'addClass')
                                  jQuery(_confirmSuccessEl).addClass(_confirmSuccessElClass);

                              else if(_confirmSuccessElAction == 'removeClass')
                                  jQuery(_confirmSuccessEl).removeClass(_confirmSuccessElClass);

                              else if(_confirmSuccessElAction == 'toggleClass')
                                  jQuery(_confirmSuccessEl).toggleClass(_confirmSuccessElClass);

                          }


                      }
                  });

              });

          } else {

              // Callback Function
              if(_confirmCallback != '' && typeof $.SOW.helper.executeFunctionByName === 'function') {
                  jQuery('.btn-confirm-yes', _target).unbind('click').on('click', function(e) {
                      e.preventDefault();
                      $.SOW.helper.executeFunctionByName(_confirmCallback, window, _t); 
                  });
              }


              jQuery('.btn-confirm-yes', _target).unbind('click').on("click", function(e) {
                  jQuery(_target).modal('hide');
              });

          }


      });

      return true;

    },





    /**
     *
     *  @ajax_confirm_template
     *
     *
     **/
    ajax_confirm_template: function() {

      // remove last
      jQuery(this.config.confirm_container).remove();
      jQuery('.modal-backdrop').remove(); // (because of vendors like nestable)

      var confirm_container = this.config.confirm_container.replace('#', ''),
          _tpl = '<div class="modal fade" id="' + confirm_container + '" role="dialog" tabindex="-1" aria-labelledby="modal-title-confirm" aria-hidden="true">'
                  + '<div class="modal-dialog '+ this.config.confirm_size +'" role="document">'

                      + '<div class="modal-content">'

                          + '<div class="modal-header border-0">'
                              
                              + '<h5 id="modal-title-confirm" class="modal-title" style="font-size:18px">'
                                  + this.config.confirm_title
                              + '</h5>'

                              + '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'

                          + '</div>'

                          + '<div class="modal-body p-3">'
                              
                              + this.config.confirm_body

                          + '</div>'

                          + '<div class="modal-footer border-top-0">'

                              + '<a href="#" class="btn py-3 btn-confirm-yes '+ this.config.btn_class_yes +'">'
                                  + this.config.btn_text_yes
                              + '</a>'

                              + '<a href="#" class="btn py-3 btn-confirm-no '+ this.config.btn_class_no +'" data-bs-dismiss="modal">'
                                  + this.config.btn_text_no
                              + '</a>'

                          + '</div>'

                      + '</div>'

                  + '</div>'
              + '</div>';


      $.SOW.globals.elBody.append(_tpl);
      $(this.config.confirm_container).modal('handleUpdate');

      // clear
      _tpl = null;

    },




    /**
     *
     *  @Return Selector
     *
     *
     **/
    __selector: function() {
      return this.selector_orig;
    }


  }

})(jQuery);
/**
 *
 *  [SOW] Form Advanced
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.file_upload.init('input[type="file"].custom-file-input, input[type="file"].form-control');
 *
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo              = 'SOW File Upload';
  window.fileInventory        = {};
  window.filesLastBulk        = [];
  window.filesSizeBulk        = 0;
  window.browser_hasIssues    = false;        // example: safari do not allow input file cloning. So we mark it as having issues


  $.SOW.core.file_upload = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {

      // toast messages
      toast_pos:                              'bottom-center',
      toast_delay:                            2000,

      browsers_with_issues:                   ['safari'],     // browsers with issues (are treated different by file input)

    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      // Mark browser with issues (eample: safari do not allow cloning input file)
      var browser_vendor  = $.SOW.helper.get_browser();
      if(this.config.browsers_with_issues.indexOf(browser_vendor) !== -1)
        window.browser_hasIssues = true;


      // 1. Has no selector
      if(!this.selector) {
        $.SOW.core.file_upload.file_upload__init('input[type="file"].custom-file-input, input[type="file"].form-control');
        return this.collection;
      }

      // 2. Has selector
      $.SOW.core.file_upload.file_upload__init(this.selector);


      // No chaining!
      return null;

    },




    /**
     *
     *  @file_upload__init
     *  File Input|Upload
     *
     *
     **/
    file_upload__init: function(_this) {

      /*

          First setup
              : unique identifier
              : empty inventory array
              : correcting human errors (like array name missing on multiple files)

      */
      jQuery(_this).each(function() {

        var _t                  = jQuery(this),
            is_multiple         = _t.attr('multiple')                       || false,
            input_name          = _t.attr('name')                           || '',
            preview_delete      = _t.data('file-ajax-delete-enable')        || false,
            preview_reorder     = _t.data('file-ajax-reorder-enable')       || false,
            fileInputIdentifier = $.SOW.helper.randomStr(4, 'N');


        // add unique identifier for each file input
        _t.attr('data-js-advanced-identifier', fileInputIdentifier);

        // create empty array
        window.fileInventory[fileInputIdentifier] = [];

        // name array missing on multiple input upload
        if(is_multiple !== false && input_name.indexOf('[]') === -1)
          _t.attr('name', input_name+'[]');


        // Used by preadded files
        if(preview_delete != false) {

          if(is_multiple != false)
            $.SOW.core.file_upload.file_upload__preview_delbtn_bind_multiple(_t, true);

          if(is_multiple == false)
            $.SOW.core.file_upload.file_upload__preview_delbtn_bind_single(_t, true);

        }

        if(preview_reorder != false)
          $.SOW.core.file_upload.file_upload__preview__reorder_bind(_t, true);


      });


      jQuery(_this).not('.js-advancified').addClass('js-advancified').on('change',function(e) {

          var _t                      = jQuery(this),
              fileInputIdentifier     = _t.data('js-advanced-identifier')                 || '', // static, real identifier for each file input
              is_multiple             = _t.attr('multiple')                               || false,
              file_name               = (is_multiple === false) ? _t.get(0).files[0].name.replace('C:\\fakepath\\', " ") : '',
              label_orig              = _t.next('.custom-file-label').attr('data-orig')   || '',
              exts_allowed            = _t.data('file-ext')                               || '',
              exist_error_txt         = _t.data('file-exist-err-msg')                     || 'File already exist: ',
              exts_error_txt          = _t.data('file-ext-err-msg')                       || 'Allowed: ',
              size_error_txt          = _t.data('file-size-err-item-msg')                 || 'File too large! ',
              size_error_txt_total    = _t.data('file-size-err-total-msg')                || 'Total size exceeded!',
              selected_txt            = _t.data('file-size-err-total-msg')                || 'selected', // in case preview is not used
              max_error_txt_total     = _t.data('file-size-err-max-msg')                  || 'Maximum allowed files:',
              toast_position          = _t.data('file-toast-position')                    || 'bottom-center',
              max_size_per_file       = _t.data('file-max-size-kb-per-file')              || 0,
              max_size_total          = _t.data('file-max-size-kb-total')                 || 0,
              max_files               = _t.data('file-max-total-files')                   || 0,
              btn_clear_files         = _t.data('file-btn-clear')                         || 'a.js-advanced-form-input-clone-clear',
              btn_submit_files        = _t.data('file-btn-submit')                        || 'button.js-advanced-form-input-clone-submit',
              preview_container       = _t.data('file-preview-container')                 || '',
              preview_height          = _t.data('file-preview-img-height')                || 120,
              preview_info            = _t.data('file-preview-show-info')                 || false,
              preview_info            = _t.data('file-preview-show-info')                 || false,
              preview_list_type       = _t.data('file-preview-list-type')                 || 'box',
              preview_class           = _t.data('file-preview-class')                     || '',
              preview_as_cover        = _t.data('file-preview-img-cover')                 || false,
              preview_reorder         = _t.data('file-ajax-reorder-enable')               || false,
              preview_delete          = _t.data('file-ajax-delete-enable')                || false,

              // @Ajax
              ajax_process_enable     = _t.data('file-ajax-upload-enable')                || false;   // false by default!




          // Force preview as cover for lists
          if(preview_list_type == 'list')
              preview_as_cover = true;


          // Some settings
          _t.attr('data-orig-name', jQuery(this).attr('name'));


          if(label_orig == '') {
              label_orig = _t.next('.custom-file-label').html() || 'Choose file';
              _t.next('.custom-file-label').attr('data-orig', label_orig);
          }


          if( exts_allowed ) {
              exts_allowed = exts_allowed.toLowerCase();
              exts_allowed = exts_allowed.split(",").map(function(ext) {
                  return ext.trim();
              });
          }


          // Always reset on safari and single file
          // SAFARI ISSUE: Cloning input file is not supported!
          if(window.browser_hasIssues === true || is_multiple === false) {

              window.fileInventory[fileInputIdentifier] = [];

              if(ajax_process_enable != true) {
                  jQuery('.js-file-input-item', preview_container).remove();
                  jQuery(btn_clear_files).addClass('hide');
              }

          }



          /*

              Single File : add file name

          */
          if(is_multiple === false)
              _t.next('.custom-file-label').text(file_name);
          else
              _t.next('.custom-file-label').text(_t.get(0).files.length + ' ' + selected_txt); // in case preview is not used





          /*

              2. Preview & Logics

          */
          var input_name          = _t.attr('name') || _t.data('orig-name'),
              _clone              = _t.clone(true).off(), // turn off binds from clone
              _bulkNo             = 'bulkNo_' + $.SOW.helper.randomStr(8),
              total_files         = _t.get(0).files.length,
              file_size_kb_bulk   = 0;


          // set back "Choose file" label
          if(is_multiple !== false)
              _t.next('.custom-file-label').html(label_orig);



          for (var i = 0; i < total_files; ++i) {

              var file            = _t.get(0).files[i],
                  file_ext        = file.name.substring(file.name.lastIndexOf('.')+1, file.name.length) || null,
                  file_size_kb    = $.SOW.helper.byte2kb(file.size),
                  _rand           = 'rand_' + $.SOW.helper.randomStr(3),
                  file_img        = '<div class="js-advanced-form-default-preview absolute-full d-table w-100" style="height:120px;"><div class="d-table-cell align-middle text-center"><span class="text-gray-500" style="font-size:30px">?</span></div></div>',
                  _concat         = file.name+':'+file.size+':'+fileInputIdentifier;
                  window.filesLastBulk.push(_rand); // because are already in the dom!

              if( file_ext ) file_ext = file_ext.toLowerCase();

              /*

                  Maximum files

              */
              if(max_files > 0) {

                  var total_files_current = jQuery('.js-file-input-item', preview_container).length;

                  if(parseInt(total_files_current) >= max_files) {

                      var _msg = max_error_txt_total + ' ' + max_files;

                      if(typeof $.SOW.core.toast === 'object') {
                          $.SOW.core.toast.show('danger', '', _msg, toast_position, 6000, true);
                      } else {
                          alert(_msg);
                      }

                      // delete input & images of this input
                      $.SOW.core.file_upload.file_upload__del_bulk(_t, _bulkNo, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);
                      return;

                  }

              }



              /*

                  Maximum total upload

              */
              if(max_size_total > 0) {

                  // because window.filesSizeBulk is calculated at the end
                  var filesSizeBulk_current = file_size_kb_bulk + window.filesSizeBulk ;

                  if(parseInt(filesSizeBulk_current) > parseInt(max_size_total)) {

                      var _msg = size_error_txt_total + ' <hr>(' + max_size_total+'kb)';

                      if(typeof $.SOW.core.toast === 'object')
                          $.SOW.core.toast.show('danger', '', _msg, toast_position, 6000, true);

                      // delete input & images of this input
                      $.SOW.core.file_upload.file_upload__del_bulk(_t, _bulkNo, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);

                      return;

                  }
              }




              // Check file size
              if(max_size_per_file > 0) {

                  if(parseInt(file_size_kb) > parseInt(max_size_per_file)) {

                      var _msg = size_error_txt + ' <hr>' + file.name +'<br>(' + file_size_kb+'kb / '+max_size_per_file+'Kb)';

                      if(typeof $.SOW.core.toast === 'object')
                          $.SOW.core.toast.show('danger', '', _msg, toast_position, 6000, true);

                      // delete input & images of this input
                      $.SOW.core.file_upload.file_upload__del_bulk(_t, _bulkNo, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);

                      return;

                  }

              }


              // Sum the size of all files in this bulk
              // if everything is ok, will add to window.filesSizeBulk
              file_size_kb_bulk += file_size_kb;


              // check allowed extension
              if(exts_allowed != '' && exts_allowed.indexOf(file_ext) === -1) {

                  var _msg = exts_error_txt + ' ' + exts_allowed.join(', ');

                  if(typeof $.SOW.core.toast === 'object') {
                      $.SOW.core.toast.show('danger', '', _msg, toast_position, 4500, true);
                  } else {
                      alert(_msg);
                  }


                  // delete input & images of this input
                  $.SOW.core.file_upload.file_upload__del_bulk(_t, _bulkNo, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);

                  return;

              }


              /*

                  File already exists

              */
              if(window.fileInventory[fileInputIdentifier].indexOf(_concat) !== -1) {

                  if(typeof $.SOW.core.toast === 'object')
                      $.SOW.core.toast.show('danger', '', exist_error_txt + '<br> '+file.name, toast_position, 4500, true);


                  // delete input & images of this input
                  $.SOW.core.file_upload.file_upload__del_bulk(_t, _bulkNo, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);

                  return;

              }


              // add file to inventory
              window.fileInventory[fileInputIdentifier].push(_concat);


              // preview image
              if(preview_container != '') {

                  var _tpl = $.SOW.core.file_upload.file_upload__preview_tpl(file, file_ext, _rand, _bulkNo, preview_height, preview_info, preview_class, ajax_process_enable, preview_delete, preview_as_cover, preview_list_type, is_multiple);
                  jQuery(preview_container).append(_tpl);

                  // bind delete button
                  if(ajax_process_enable === true) {

                      if(preview_delete != false) {

                          if(is_multiple != false)
                              $.SOW.core.file_upload.file_upload__preview_delbtn_bind_multiple(_t, false);

                          if(is_multiple == false)
                              $.SOW.core.file_upload.file_upload__preview_delbtn_bind_single(_t, false);

                      }

                      if(preview_reorder === true)
                          $.SOW.core.file_upload.file_upload__preview__reorder_bind(_t, false);

                  }

                  // image preview ---------------------------------------
                  if (file_ext == "gif" || file_ext == "png" || file_ext == "jpg" || file_ext == "jpeg")
                      $.SOW.core.file_upload.file_upload__preview_img_render('#'+_rand, file, preview_as_cover, preview_list_type);

              }


          }


          // show `remove all files` button
          $.SOW.core.file_upload.file_upload__btn_clear_bind(btn_clear_files, btn_submit_files, _t, preview_container, input_name, fileInputIdentifier);



          // 1. append cloned element
          // safari DO NOT support file input cloning!
          // also not allowing hidden file inputs - so we use .viewport-out class, just in case, for future chrome/etc updates
          if(window.browser_hasIssues !== true) {

              // hard to debug with all variables. We remove them, never used as a clone!
              _clone.removeAttr('class name data-orig-name aria-describedby data-file-ajax-upload-url data-file-ajax-upload-params data-file-ajax-delete-params data-file-ajax-callback-function data-file-ajax-toast-success-txt data-file-ajax-toast-error-txt data-file-ajax-upload-enable data-file-ajax-reorder-enable data-file-ajax-reorder-url data-js-advanced-identifier data-js-file-input-clone js-advanced-identifier data-file-ext data-file-max-size-kb-per-file data-file-max-size-kb-total data-file-max-total-files data-file-ext-err-msg data-file-exist-err-msg data-file-size-err-item-msg data-file-size-err-total-msg data-file-size-err-max-msg data-file-toast-position data-file-preview-container data-file-preview-img-height data-file-preview-show-info data-file-btn-clear data-file-preview-class data-file-preview-img-cover file-preview-list-type data-file-ajax-progressbar-custom data-file-ajax-progressbar-disable');
              _clone.attr('id', _bulkNo).attr('name', input_name);

              /*

                  is_multiple !== false
                  Used with "required" attribute (single file)

              */
              if( is_multiple !== false )
                  _t.val('').removeAttr('name').attr('data-orig-name', input_name);
              // -- --

              // This container holds all clonned inputs
              if(jQuery('#js_advanced_form_identifier_'+fileInputIdentifier).length < 1) {
                  var __el = _t.next();

                  // If input is inside a <label>
                  if(_t.parents('label').length > 0)
                      __el = _t.parents('label');

                  // bootstrap custom file
                  else if(_t.parents('div.custom-file').length > 0) {
                      __el = _t.parents('div.custom-file');

                      if(_t.parents('div.input-group').length > 0)
                          __el = _t.parents('div.input-group');

                  }

                  // If input is directly inside a <form>
                  if(_t.parent('form').length > 0)
                      __el = _t;

                  __el.after('<div id="js_advanced_form_identifier_'+fileInputIdentifier+'" class="viewport-out opacity-0" style="width:50px;height:50px;"></div>');
              }

              jQuery('#js_advanced_form_identifier_'+fileInputIdentifier).append(_clone);

              window.filesLastBulk = [];
              window.filesSizeBulk += file_size_kb_bulk;

          }




          /**

              AJAX : UPLOAD

          **/
          if(ajax_process_enable === true) {

              // Block input
              _t.addClass('disabled').prop('disabled', true);
              _t.parents('.btn').addClass('disabled active');

              // Wait a little bit for the previews to show up!
              setTimeout(function() {

                  // are added back by ajax if no error
                  _t.removeClass('disabled').prop('disabled', false);
                  _t.parents('.btn').removeClass('disabled active');
                  $.SOW.core.file_upload.file_upload__ajax_upload(fileInputIdentifier, _bulkNo);

              }, 350);
          }

          // console.log(window.fileInventory);

      });

    },

        // reset to intial state
        file_upload__reset: function(_t, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier) {

            jQuery('.js-file-input-item', preview_container).remove();
            jQuery(btn_clear_files).addClass('hide');
            jQuery(btn_submit_files).addClass('hide');

            // restoring back to original
            _t.val('').attr('name', input_name).removeAttr('data-orig-name').prop('disabled', false);

            // remove the entire container holding cloned/inputs
            jQuery('#js_advanced_form_identifier_'+fileInputIdentifier).remove();

            // set back "Choose file" label
            var label_orig = _t.next('.custom-file-label').attr('data-orig') || 'Choose file';
                _t.next('.custom-file-label').html(label_orig);

            window.fileInventory[fileInputIdentifier] = [];
            window.filesLastBulk = [];

        },

        // clear input values
        file_upload__clear: function(fileInputIdentifier) {

            jQuery('input[type="file"][data-js-advanced-identifier="'+fileInputIdentifier+'"]').val('').prop('disabled', false);

        },

        file_upload__preview_tpl: function(file, file_ext, item_unique_id, _bulkNo, h, preview_info, preview_class, ajax_process_enable, preview_delete, preview_as_cover, preview_list_type, is_multiple) {



            var default_preview_height  = (h == 'auto' || h == '100%') ? h : 120,
                img_fluid               = '';


            if(typeof h !== 'number') {

                switch(preview_list_type) {

                    case 'list':        var h = 50;
                                        break;

                    // 'box'
                    default:            var h = default_preview_height;
                                        var img_fluid = 'img-fluid';

                }

            }

            // 50px min. height for `list` type
            if(preview_list_type == 'list' && h < 50)
                var h = 50;

            var __h = (typeof h !== 'number') ? '100%' : h + 'px';


            switch(preview_as_cover) {

                case true:          var item_style = 'width: '+__h+'; height: '+__h+';';
                                    break;

                // false
                default:            var item_style = 'min-width: '+__h+'; min-height: '+__h+'; width: auto; height: auto;';

            }


            var _loadingIcon        = '<i class="' + $.SOW.config.sow__icon_loading + ' fs--30"></i>';
            var _loadingIconSize    = (preview_list_type == 'list') ? 'fs--20' : 'fs--30';

            // default preview class
            if(preview_class == '')
                var preview_class = (preview_list_type == 'list') ? 'show-hover-container shadow-md mb-2 rounded' : 'show-hover-container shadow-md m-2 rounded';

            // Default : Show Loading
            var file_img = '<span class="js-advanced-form-default-preview absolute-full d-flex align-items-center justify-content-center opacity-6 '+_loadingIconSize+' '+$.SOW.config.sow__icon_loading+'"></span>';

            // bytes to size
            var human_size = $.SOW.helper.byte2size(file.size);

            // Image -or- extension
            // Default append. src = transaprent png base64
            var file_is_image = false;
            if (file_ext == "gif" || file_ext == "png" || file_ext == "jpg" || file_ext == "jpeg" || file_ext == "jpeg") {
                var file_is_image = true;
                file_img += '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" class="hide animate-bouncein js-file-input-preview mw-100 '+img_fluid+'" height="'+h+'">';
            }

            else if (file_ext != '') {
                var file_ext_font_size = (h < 80) ? 'fs--20' : 'fs--30';
                var file_ext_font_size = (preview_list_type == 'list') ? 'fs--18' : file_ext_font_size;
                var file_img = '<span class="absolute-full d-flex align-items-center justify-content-center '+file_ext_font_size+' opacity-6 text-uppercase text-truncate">'+file_ext+'</span>';
            }

            var pos_start   = ($.SOW.globals.direction == 'ltr') ? 'left-0' : 'right-0';
            var pos_end     = ($.SOW.globals.direction == 'ltr') ? 'right-0' : 'left-0';






            // 1. LIST : START TEMPLATE
            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
            if(preview_list_type == 'list') {

                var _tpl = '<div data-id="0" data-file-name="'+file.name+'" data-file-size="'+file.size+'" id="'+item_unique_id+'" class="js-file-input-item '+_bulkNo+' d-flex clearfix position-relative '+preview_class+'">';

                    // delete button
                    if(preview_delete != false) {

                        _tpl += '<a href="#" data-item-id="'+item_unique_id+'" data-id="0" data-file-name="'+file.name+'" class="js-file-item-del position-absolute absolute-top show-hover-item '+pos_end+' z-index-2">'
                                    + '<span class="d-inline-block btn btn-sm bg-danger text-white" style="padding:4px 10px;margin:1px;">'
                                        + '<i class="'+$.SOW.config.sow__icon_close+' m-0"></i>'
                                    + '</span>'
                                + '</a>';

                    }

                        // image | file                                                         required min-* because of flex...
                        _tpl += '<div class="position-relative d-inline-block bg-cover" style="width:'+h+'px; min-width:'+h+'px; height:'+h+'px">' + file_img + '</div>';

                        _tpl += '<div class="flex-fill d-flex min-w-0 align-items-center" style="padding-left:15px;padding-right:15px;">';

                            // file name
                            _tpl += '<span class="text-truncate d-block line-height-1">' + file.name;

                                // file size
                                if(preview_info != false)
                                    _tpl += '<br><span style="font-size:12px;" class="d-block pt-1">'+human_size+'</span>';

                            _tpl += '</span>';

                        _tpl += '</div>';

                    // ajax loading
                    if(ajax_process_enable === true) {
                        _tpl += '<span class="hide hide-force js-file-input-preview-ajax-uploading absolute-full d-flex align-items-center justify-content-center overlay-light overlay-opacity-8 z-index-2" style="padding-left:15px;padding-right:15px;">'
                                + _loadingIcon
                             + '</span>';
                    }


                _tpl += '</div>';

                return _tpl;

            }
            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --







            // 2. BOX : START TEMPLATE
            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
            var _tpl = '<span data-id="0" data-file-name="'+file.name+'" data-file-size="'+file.size+'" id="'+item_unique_id+'" class="js-file-input-item '+_bulkNo+' d-inline-block position-relative overflow-hidden text-center '+preview_class+'" style="'+item_style+'">';

            _tpl += (preview_as_cover !== false) ? '' : file_img;
            // _tpl += '<span class="overlay-dark overlay-opacity-1 absolute-full"></span>'


            // header (file name) to non-image files!
            if(file_is_image === false || preview_info != false) {

                _tpl += '<span class="js-file-input-preview-header text-white position-absolute w-100 top-0 left-0 right-0 clearfix overlay-dark overlay-opacity-2">'
                        + '<span class="text-truncate d-block" style="font-size:12px;padding-left:5px;padding-right:5px;">' + file.name + '</span>'
                     + '</span>';
            }

            // footer
            if(preview_info != false) {

                // file will be populated (or not) by reader (reason: async)
                _tpl += '<span class="js-file-input-preview-footer text-white position-absolute w-100 bottom-0 left-0 right-0 clearfix overlay-dark overlay-opacity-2">'
                            + '<span class="js-file-input-file-info-size text-truncate d-block" style="font-size:12px;padding-left:5px;padding-right:5px;">'+human_size+'</span>'
                      + '</span>';

            }

            // ajax only
            if(ajax_process_enable === true) {

                // delete button
                if(preview_delete != false && is_multiple != false) {

                    _tpl += '<a href="#" data-item-id="'+item_unique_id+'" data-id="0" data-file-name="'+file.name+'" class="js-file-item-del position-absolute absolute-top show-hover-item '+pos_start+' z-index-2">'
                                + '<span class="d-inline-block btn btn-sm bg-danger text-white" style="font-size:12px;padding:4px 8px;margin:1px;">'
                                    + '<i class="'+$.SOW.config.sow__icon_close+' m-0"></i>'
                                + '</span>'
                            + '</a>';
                }

                // ajax loading
                _tpl += '<span class="hide hide-force js-file-input-preview-ajax-uploading absolute-full d-flex align-items-center justify-content-center overlay-light overlay-opacity-8 z-index-3">'
                        +_loadingIcon
                     + '</span>';

            }


            _tpl += '</span>';

            return _tpl;
            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


        },

        file_upload__preview_img_render: function(id, file, preview_as_cover, preview_list_type) {

            var reader = new FileReader();
            reader.onload = function(s) {

                jQuery(id + ' .js-advanced-form-default-preview').remove();

                if(preview_as_cover !== false) {

                    var __id = (preview_list_type == 'list') ? id + '>.bg-cover' : id;
                    jQuery(__id).css("background-image", "url(" + s.target.result + ")").addClass('bg-cover').removeClass('hide');

                } else {

                    jQuery(id + ' img.js-file-input-preview').attr('src', s.target.result).removeClass('hide');

                }

            }

            reader.readAsDataURL(file);

        },

        // delete last bulk added (multiple file only)
        file_upload__del_bulk: function(_t, _bulkNo, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier) {

            // clear last added!
            for (var b = 0; b < window.filesLastBulk.length; ++b) {

                // remove last bulk added only
                jQuery('#'+_bulkNo).remove(); // file input
                jQuery('.'+_bulkNo).remove(); // images

                // check if other files are there. if not, reset
                var _remains = jQuery('.js-file-input-item', preview_container).length;
                if(_remains < 1)
                    $.SOW.core.file_upload.file_upload__reset(_t, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);

            }

            // reset
            $.SOW.core.file_upload.file_upload__clear(fileInputIdentifier);
            window.fileInventory[fileInputIdentifier] = [];

        },

        file_upload__preview_delbtn_bind_multiple: function(_t, request_from_preadded) {

            var fileInputIdentifier = _t.attr('data-js-advanced-identifier')    || '',
                preview_container   = _t.attr('data-file-preview-container')    || '',
                preview_delete      = _t.attr('data-file-ajax-delete-enable')   || false,
                ajax_url            = _t.attr('data-file-ajax-delete-url')      || _t.attr('data-file-ajax-upload-url'),
                ajax_params         = _t.attr('data-file-ajax-delete-params')   || '',
                delToastTxt         = _t.attr('data-file-ajax-delete-toast-txt')        || 'Successfully Deleted!',
                delToastPos         = _t.attr('data-file-ajax-delete-toast-position')   || $.SOW.core.file_upload.config.toast_pos,
                is_multiple         = _t.attr('multiple')                       || false,
                preview_height      = _t.attr('data-file-preview-img-height')   || '120';

            if(preview_container == '' || is_multiple == false)
                return;

            else if(ajax_url == '' || preview_delete == false) {

                // remove delete button
                jQuery(preview_container + ' a.js-file-item-del').remove();

                return;
            }

            // --


            // Setting up preadded files
            if(request_from_preadded === true) {
                jQuery(preview_container + ' .js-file-input-item').each(function() {

                    var elItemPreadded      = jQuery(this),
                        _preaddedItemId     = 'rand_' + $.SOW.helper.randomStr(3);

                    elItemPreadded.attr('id', _preaddedItemId);

                    jQuery('img', elItemPreadded).attr('height', preview_height);
                    jQuery('a.js-file-item-del', elItemPreadded).attr('data-item-id', _preaddedItemId);         // DOM item id

                });
            }

            var btn_file_del = preview_container + ' a.js-file-item-del';
            $.SOW.core.file_upload.file_upload__preview_item_del_ajax(fileInputIdentifier, btn_file_del, ajax_params, ajax_url, delToastTxt, delToastPos);

        },

        file_upload__preview_delbtn_bind_single: function(_t, request_from_preadded) {

            var fileInputIdentifier = _t.attr('data-js-advanced-identifier')    || '',
                preview_container   = _t.attr('data-file-preview-container')    || '',
                preview_delete      = _t.attr('data-file-ajax-delete-enable')   || false,
                ajax_url            = _t.attr('data-file-ajax-delete-url')      || _t.attr('data-file-ajax-upload-url'),
                ajax_params         = _t.attr('data-file-ajax-delete-params')   || '',
                delToastTxt         = _t.attr('data-file-ajax-delete-toast-txt')        || 'Successfully Deleted!',
                delToastPos         = _t.attr('data-file-ajax-delete-toast-position')   || $.SOW.core.file_upload.config.toast_pos,
                btn_clear           = _t.attr('data-file-btn-clear')            || '',
                btn_submit          = _t.attr('data-file-btn-submit')           || '',
                is_multiple         = _t.attr('multiple')                       || false;

            if(is_multiple != false || btn_clear == '' || ajax_url == '' || preview_delete == false)
                return;

            // We use the same button as "delete" button normally added by the template
            jQuery(btn_clear).addClass('js-file-item-del');

            // Setting up
            var elItemPreadded      = jQuery(preview_container + ' .js-file-input-item'),
                _preaddedFileName   = elItemPreadded.attr('data-file-name')     || '',
                _preaddedItemId     = elItemPreadded.attr('id')                 || '';

            if(elItemPreadded.length < 1)
                return;

            // Disable input
            _t.prop('disabled', true);

            if(_preaddedItemId == '') {
                _preaddedItemId = 'rand_' + $.SOW.helper.randomStr(3);
                elItemPreadded.attr('id', _preaddedItemId);
            }

            jQuery(btn_clear).attr('data-item-id', _preaddedItemId);        // DOM item id

            $.SOW.core.file_upload.file_upload__preview_item_del_ajax(fileInputIdentifier, btn_clear, ajax_params, ajax_url, delToastTxt, delToastPos);

            // REQUIRED: AFTER file_upload__preview_item_del_ajax()
            if(request_from_preadded === true) {
                var input_name  = _t.attr('name') || _t.data('orig-name');
                $.SOW.core.file_upload.file_upload__btn_clear_bind(btn_clear, btn_submit, _t, preview_container, input_name, fileInputIdentifier);
            }

        },

        file_upload__preview_item_del_ajax: function(fileInputIdentifier, btn_file_del, ajax_params, ajax_url, delToastTxt, delToastPos) {

            jQuery(btn_file_del).not('.js-advancified').addClass('js-advancified').on('click', function(e) {
                e.preventDefault();

                var elBtn       = jQuery(this),
                    item_id     = elBtn.attr('data-item-id')                    || '';

                if(item_id == '') // required
                    return;

                var elItem      = $('#'+item_id),
                    fileName    = elItem.attr('data-file-name')                 || '',
                    fileSize    = elItem.attr('data-file-size')                 || 0,
                    fileID      = elItem.attr('data-id')                        || 0;

                if(fileName == '' && item_id == '') {
                    $.SOW.helper.consoleLog('Delete request not sent!');
                    $.SOW.helper.consoleLog('Please provide: data-id  -OR-  data-file-name ');
                    return;
                }


                /*

                    Form & custom params

                */
                var formDataDel = new FormData();
                formDataDel.append('action', 'delete_file');
                formDataDel.append('ajax', 'true');

                var ajax_params_arr = $.SOW.helper.params_parse(ajax_params);
                for (var i = 0; i < ajax_params_arr.length; ++i) {
                    formDataDel.append(ajax_params_arr[i][0], ajax_params_arr[i][1]);
                }

                // Add required params to form
                formDataDel.append('file_name', fileName);
                formDataDel.append('file_id', fileID);
                formDataDel.append('__info', '`file_id` = internal/database file id. 0 = not provided!');

                // --

                $.ajax({
                    url:            ajax_url,
                    cache:          false,
                    contentType:    false,
                    processData:    false,
                    data:           formDataDel,
                    type:           'POST',
                    headers:        '',
                    crossDomain:    '',

                    beforeSend: function() {


                    },

                    success: function (data) {

                        window.ddimgCache = ''; // dropdown ajax image
                        $.SOW.helper.consoleLog(data);

                        // remove file from the list
                        if(item_id != '')
                            jQuery('#'+item_id).remove();



                        // remove from inventory, or the file could not be added again
                        if(fileSize != '') {

                            var _concat = fileName+':'+fileSize+':'+fileInputIdentifier;
                            for (var inventoryIndex in window.fileInventory[fileInputIdentifier]) {
                                if(window.fileInventory[fileInputIdentifier][inventoryIndex] == _concat) {
                                    window.fileInventory[fileInputIdentifier].splice(inventoryIndex,1);
                                    break;
                                }
                            } // --

                        }

                        // delete success toast
                        if(typeof $.SOW.core.toast === 'object' && delToastTxt != '')
                            $.SOW.core.toast.show('success', '', delToastTxt, delToastPos, $.SOW.core.file_upload.config.toast_delay, true);

                    },

                    error: function (data) {

                        $.SOW.helper.consoleLog(data);

                        if(typeof $.SOW.core.toast === 'object') {
                            $.SOW.core.toast.show('danger', '', '404 Server Error!', $.SOW.core.file_upload.config.toast_pos, $.SOW.core.file_upload.config.toast_delay, true);
                        } else {
                            alert('404 Server Error!');
                        }

                    },

                });

            });

        },

        file_upload__preview__reorder_bind: function(_t, request_from_preadded) {

            var sortableProcessDelay = 0;

            if(typeof $.SOW.vendor.sortable !== 'object') {

                $.SOW.helper.consoleLog('Ajax Image Reorder : Require Sortablejs Vendor!');
                return;

            } else {

                // sortable is external?
                if (typeof Sortable !== 'function') {

                    var sortableProcessDelay = 1300;

                    if($.SOW.config.autoinit['sortable']) {
                        var _selector   = $.SOW.config.autoinit['sortable'][1];
                        var _config     = $.SOW.config.autoinit['sortable'][2];
                        var _class      = _selector.replace('.', '');
                        jQuery('#middle').append('<span class="hide '+_class+'"></span>');
                        $.SOW.vendor.sortable.init(_selector, _config);
                    } else { return false; }
                }

            }


            var fileInputIdentifier     = _t.attr('data-js-advanced-identifier')            || '',
                fileInputName           = _t.attr('name')                                   || _t.attr('data-orig-name'),
                preview_container       = _t.attr('data-file-preview-container')            || '',
                ajax_url                = _t.attr('data-file-ajax-reorder-url')             || _t.attr('data-file-ajax-upload-url'),
                ajax_params             = _t.attr('data-file-ajax-reorder-params')          || '',
                reorder_toast_success   = _t.attr('data-file-ajax-reorder-toast-success')   || '',
                reorder_toast_position  = _t.attr('data-file-ajax-reorder-toast-position')  || '',
                preview_reorder         = _t.attr('data-file-ajax-reorder-enable')          || false;

            if(preview_container == '')
                return;

            else if(ajax_url == '' || preview_reorder == false) {

                // remove draggable attribute
                jQuery(preview_container + ' .js-file-input-item').removeAttr('draggable');

                return;

            }

            // --

            // add .js-ignore filter for all other items that are not files (texts, titles, etc)
            jQuery('>:not(.js-file-input-item)', preview_container).addClass('js-ignore');

            // Add Sortable required params
            jQuery(preview_container).attr('data-ajax-update-url', ajax_url);
            jQuery(preview_container).attr('data-ajax-update-params', ajax_params);
            jQuery(preview_container).attr('data-ajax-update-identifier', fileInputName.replace('[]', ''));

            if(reorder_toast_success != '')
                jQuery(preview_container).attr('data-update-toast-success', reorder_toast_success);

            if(reorder_toast_position != '')
                jQuery(preview_container).attr('data-update-toast-position', reorder_toast_position);

            setTimeout(function() {
                $.SOW.vendor.sortable.process($(preview_container));
            }, sortableProcessDelay);

        },

        file_upload__btn_clear_bind: function(btn_clear_files, btn_submit_files, _t, preview_container, input_name, fileInputIdentifier) {

            // show `remove all files` button
            jQuery(btn_clear_files).removeClass('hide').on('click', function(e) {
                e.preventDefault();

                // added by file_upload__preview_delbtn_bind_single()
                jQuery(this).removeAttr('data-item-id data-id data-file-name');

                $.SOW.core.file_upload.file_upload__reset(_t, btn_clear_files, btn_submit_files, preview_container, input_name, fileInputIdentifier);
            });

            // submit button
            jQuery(btn_submit_files).removeClass('hide');

        },

        file_upload__ajax_upload: function(fileInputIdentifier, _bulkNo) {


            if(_bulkNo == '' && fileInputIdentifier == '') {
                $.SOW.helper.consoleLog('Ajax Image Upload Status : Identifiers missing!');
                return;
            }


            // Getting Data
            var fileInventory       = [],
                elFileInput         = jQuery('input[type="file"][data-js-advanced-identifier="'+fileInputIdentifier+'"]'),
                ajax_url            = elFileInput.data('file-ajax-upload-url')              || '',
                ajax_params         = elFileInput.data('file-ajax-upload-params')           || '',
                msg_success         = elFileInput.data('file-ajax-toast-success-txt')       || '',
                msg_error           = elFileInput.data('file-ajax-toast-error-txt')         || '',
                btn_remove          = elFileInput.data('file-btn-clear')                    || '',
                form_name           = elFileInput.attr('name')                              || elFileInput.data('orig-name'),
                ajax_callback       = elFileInput.data('file-ajax-callback-function')       || '',
                is_multiple         = elFileInput.attr('multiple')                          || false,
                preview_container   = elFileInput.data('file-preview-container')            || '',
                preview_reorder     = elFileInput.data('file-ajax-reorder-enable')          || false,
                progressDisabled    = elFileInput.data('file-ajax-progressbar-disable')     || false,
                toast_position      = elFileInput.data('file-toast-position')               || $.SOW.core.file_upload.config.toast_pos;


            // search for from as parent, if exists
            if(ajax_url == '') {

                if(elFileInput.parents('form').length > 0)
                    ajax_url = elFileInput.parents('form').attr('action');

            }

            // still empty?
            if(ajax_url == '') {
                $.SOW.helper.consoleLog('Ajax Image Upload Status : [data-file-ajax-upload-url] : Missing -or- Empty!');
                return;
            }











            /*

                1. Bulk only

            */
            if(_bulkNo != '') {

                if(window.browser_hasIssues === true) {
                    var _t = jQuery('input[type="file"][data-js-advanced-identifier="'+fileInputIdentifier+'"]');
                } else {
                    var _t = jQuery('input[type="file"]#'+_bulkNo);
                }


                for (var i = 0; i < _t.get(0).files.length; ++i) {
                    fileInventory.push(_t.get(0).files[i]);
                }


            /*

                2. All bulks at once (for safari or other browsers with issue)

            */
            } else {

                if(window.browser_hasIssues === true) {
                    var _t = jQuery('input[type="file"][data-js-advanced-identifier="'+fileInputIdentifier+'"]');
                } else {
                    var _t = jQuery('#js_advanced_form_identifier_'+fileInputIdentifier+'>input[type="file"]');
                }


                _t.each(function() {

                    var files   = jQuery(this).get(0).files;

                    for (var i = 0; i < files.length; ++i) {
                        fileInventory.push(files[i]);
                    }

                });

            }







            // Safe Check
            if(fileInventory.length < 1) {
                $.SOW.helper.consoleLog('Ajax Image Upload Status : No file found to upload!');
                return;
            }




            // Create form
            var formData = new FormData();
            // -- -- -- --





            // Add custom params to post +++++++++++++++++++++++++++++++++++++++
            var ajax_params_arr = $.SOW.helper.params_parse(ajax_params);
            for (var i = 0; i < ajax_params_arr.length; ++i) {
                formData.append(ajax_params_arr[i][0], ajax_params_arr[i][1]);
            }

            // Of course, our eternal param :)
            formData.append('ajax', 'true');
            // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++





            // add files to form +++++++++++++++++++++++++++++++++++++++++++++++
            for (var i = 0; i < fileInventory.length; ++i) {
                formData.append(form_name, fileInventory[i]);
            }
            // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++





            $.ajax({
                url:            ajax_url,
                cache:          false,
                contentType:    false,
                processData:    false,
                data:           formData,
                type:           'POST',
                enctype:        'multipart/form-data',
                headers:        '',
                crossDomain:    '',

                beforeSend: function() {

                    jQuery('.js-file-input-item.'+_bulkNo+' .js-file-input-preview-ajax-uploading').removeClass('hide hide-force');
                    // jQuery(btn_remove).addClass('hide hide-force'); // button "remove files"

                    elFileInput.addClass('disabled').prop('disabled', true);
                    elFileInput.parents('.btn').addClass('disabled active');
                    elFileInput.parent().find('.js-file-input-ajax-loader').removeClass('hide hide-force');

                    // Create progress bar
                    $.SOW.core.file_upload.file_upload__ajax_upload_progressbar_init(elFileInput);

                },

                success: function (data) {

                    window.ddimgCache = ''; // dropdown ajax image

                    $.SOW.helper.consoleLog(data);
                    elFileInput.removeClass('disabled').prop('disabled', false);
                    elFileInput.parents('.btn').removeClass('disabled active');
                    elFileInput.parent().find('.js-file-input-ajax-loader').addClass('hide hide-force');

                    jQuery('.js-file-input-item.'+_bulkNo+' .js-file-input-preview-ajax-uploading i').replaceWith('<i class="'+$.SOW.config.sow__icon_check+' text-success d-inline-block text-center bg-success-soft rounded-circle opacity-8 animate-bouncein" style="font-size:20px;padding-top:5px;width:40px;height:40px;"></i>');
                    setTimeout(function() {

                        jQuery('.js-file-input-item.'+_bulkNo+' .js-file-input-preview-ajax-uploading').fadeOut(200, function() {
                            jQuery(this).remove();
                        });

                    }, $.SOW.core.file_upload.config.toast_delay);


                    if(typeof $.SOW.core.toast === 'object' && msg_success != '')
                        $.SOW.core.toast.show('success', '', msg_success, toast_position, $.SOW.core.file_upload.config.toast_delay, true);


                    // callback function
                    if(ajax_callback != '' && typeof $.SOW.helper.executeFunctionByName === "function")
                        $.SOW.helper.executeFunctionByName(ajax_callback, window, data, elFileInput); // input element is returned because of safari issues (bulk is empty)

                    // Destroy progress bar
                    $.SOW.core.file_upload.file_upload__ajax_upload_progressbar_destroy(elFileInput, false);


                    // Disable input
                    if(is_multiple === false)
                        elFileInput.prop('disabled', true);

                    /**

                        UPDATE FOR REORDER
                        If backend provided required data (internal id)

                        data-id="0"
                        data-file-name="file.jpg"


                        PHP Example
                        Server response format:

                            $response = array(
                                'file_list' => array(

                                    // original file name       internal id
                                    'orig_file_name_1.jpg'      => 1,
                                    'orig_file_name_2.jpg'      => 345,

                                    // ...

                                ),

                                // more stuff for callback (if used)
                                'something_else'    => array(),
                            );

                            echo json_encode($response);
                            exit;

                    **/
                    // if(preview_reorder != false) {

                        /* test */ // var data = {"file_list":{"orig_file_name_1.jpg":1,"orig_file_name_2.jpg":345},"something_else":[]}
                        try {

                            var _json = JSON.parse(data);

                        } catch(err) {

                            // No json, we do nothing!
                            var _json = '';

                        }

                        if(_json != '') {

                            if(typeof _json['file_list'] !== 'undefined') {

                                for (var fileName in _json['file_list']) {

                                    jQuery(preview_container + ' .js-file-input-item[data-file-name="'+fileName+'"]').attr('data-id', _json['file_list'][fileName]);

                                }

                            }

                            // Bind reorder
                            $.SOW.core.file_upload.file_upload__preview__reorder_bind(jQuery(preview_container), true);

                        }

                    // } // end preview reorder

                },

                error: function (data) {

                    $.SOW.helper.consoleLog(data);

                    elFileInput.removeClass('disabled').prop('disabled', false);
                    elFileInput.parents('.btn').removeClass('disabled active');
                    elFileInput.parent().find('.js-file-input-ajax-loader').addClass('hide hide-force');

                    jQuery('.js-file-input-item.'+_bulkNo+' .js-file-input-preview-ajax-uploading i').replaceWith('<i title="Ajax upload error!" class="'+$.SOW.config.sow__icon_close+' text-success d-inline-block text-center bg-danger-soft rounded-circle opacity-8 animate-bouncein" style="font-size:20px;padding-top:5px;width:40px;height:40px;"></i>');
                    if(typeof $.SOW.core.toast === 'object' && msg_error != '')
                        $.SOW.core.toast.show('danger', '', msg_error, toast_position, $.SOW.core.file_upload.config.toast_delay, true);

                    // Destroy progress bar
                    $.SOW.core.file_upload.file_upload__ajax_upload_progressbar_destroy(elFileInput, false);

                },

                // progress bar
                xhr: function() {

                    var __xhr = new XMLHttpRequest();

                    __xhr.upload.addEventListener("progress", function(e) {

                        if(e.lengthComputable && progressDisabled == false) {

                            var ___percent  = e.loaded / e.total,
                                ___percent  = Math.round(___percent * 100);     // (___percent * 100);  -or-  Math.round(___percent * 100);

                            $('.js_file_input_progress__'+fileInputIdentifier + ' .js-file-input-upload-percent').text(___percent+'%');
                            $('.js_file_input_progress__'+fileInputIdentifier + ' .progress-bar').css('width', ___percent+'%');
                            $('.js_file_input_progress__'+fileInputIdentifier + ' .progress-bar').attr('aria-valuenow', ___percent);

                            if(___percent >= 100) {

                                // we add "ok" icon only to the dynamic progress created by this script, not to custom
                                $('.js_file_input_progress__'+fileInputIdentifier + ' .js-file-input-upload-percent.js-file-input-upload-percent-dynamic').prepend('<i class="'+$.SOW.config.sow__icon_check+' animate-bouncein" style="font-size:10px"></i> &nbsp; ');

                                // we add some common classes as helpers to custom progress, in case needed as extra!
                                $('.js_file_input_progress__'+fileInputIdentifier + ', .js_file_input_progress__'+fileInputIdentifier + ' .js-file-input-upload-percent:not(.js-file-input-upload-percent-dynamic) .group-icon').addClass('active');

                            }

                        }

                    }, false);

                    return __xhr;

                }


            });

        },

        file_upload__ajax_upload_progressbar_init: function(elFileInput) {

            if(elFileInput.length < 1)
                return;

            var fileInputIdentifier = elFileInput.data('js-advanced-identifier')            || '',
                customProgress      = elFileInput.data('file-ajax-progressbar-custom')      || '',
                progressDisabled    = elFileInput.data('file-ajax-progressbar-disable')     || false,
                __next              = elFileInput.next();

            if(fileInputIdentifier == '' || progressDisabled != false)
                return;







            // ++ ++ ++ ++ ++ ++ ++ Check custom progress ++ ++ ++ ++ ++ ++
            if(jQuery(customProgress).length > 0) {

                var elCustomProgress = jQuery(customProgress);

                // usually first init, should have the class in place each call (new upload)
                if(!elCustomProgress.hasClass('js_file_input_progress__' + fileInputIdentifier))
                    elCustomProgress.addClass('js_file_input_progress__' + fileInputIdentifier);


                // Reset progress. If previously used should be at 100% now
                jQuery('.progress-bar', elCustomProgress).css('width', '0%');
                jQuery('.progress-bar', elCustomProgress).css('width', '0%');
                jQuery('.js-file-input-upload-percent', elCustomProgress).text('0%');
                elCustomProgress.removeClass('hide hide-force');


                // Done! Ajax function will do the update!
                return;

            }
            // ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++







            // ++ ++ ++ ++ ++ Finding a place for progress bar ++ ++ ++ ++ ++
            // If input is inside a <label>
            if(elFileInput.parents('label').length > 0)
                __next = elFileInput.parents('label');

            // bootstrap custom file
            else if(elFileInput.parents('div.custom-file').length > 0)
                __next = elFileInput.parents('div.custom-file');
            // ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++



            // Remove first, in case already exists!
            jQuery('.js_file_input_progress__'+fileInputIdentifier).remove();



            // PROGRESS TEMPLATE ++++++++++++
            var _tpl = '<span class="js_file_input_progress__'+fileInputIdentifier+' animate-bouncein transition-all-ease-500 position-absolute w-100 bottom-0 left-0 right-0" style="margin-bottom:-5px;heitgh:3px;">'
                            + '<span class="progress rounded" style="height:3px">'
                                + '<span class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></span>'
                            + '</span>'
                            + '<i class="js-file-input-upload-percent js-file-input-upload-percent-dynamic float-start z-index-2 text-dark" style="font-size:11px; margin-bottom:-20px;">0%</i>'
                        + '</span>';
            // ++++++++++++++++++++++++++++++
            __next.append(_tpl);    // Append inside, absolute position used!
            // ++++++++++++++++++++++++++++++


            // Done! Ajax function will do the update!

        },

        file_upload__ajax_upload_progressbar_destroy: function(elFileInput, elDestroyForReal) {


            if(elFileInput.length < 1)
                return;

            var fileInputIdentifier = elFileInput.data('js-advanced-identifier') || '';

            if(fileInputIdentifier == '')
                return;



            // Wait a little bit, sometimes the upload is too fast for small files
            // and we want to impress the visitor with our shiny progress bar pointing to 100% DONE!
            // Whooo!
            setTimeout(function() {

                jQuery('.js_file_input_progress__'+fileInputIdentifier).not('.js-ignore').fadeOut(800, function() {


                    /*

                        Should be removed only dynamic progress bars
                        Custom ones should only be hidden, if .js-ignore not present, of course!

                    */
                    if(elDestroyForReal === true)
                        jQuery(this).remove();

                });

            }, 1000);

        },

        file_upload__destroy: function(container) {
            var container = (typeof container === 'undefined') ? '' : container;

            jQuery(container + ' ' + $.SOW.core.file_upload.config.selector_advanced_file).each(function() {

                var _t          = jQuery(this),
                    input_name  = _t.attr('name') || _t.data('orig-name');

                // remove class
                _t.removeClass('js-advancified');

                // destroy any progress bar
                $.SOW.core.file_upload.file_upload__ajax_upload_progressbar_destroy(_t, true);

                // restoring back to original
                _t.val('').attr('name', input_name).removeAttr('data-orig-name');

                // set back "Choose file" label
                var label_orig = _t.next('.custom-file-label').attr('data-orig') || 'Choose file';
                _t.next('.custom-file-label').html(label_orig);

                // replace with a clone!
                var _cl = _t.clone().off('change');
                _t.replaceWith(_cl);

                jQuery('div>.js-file-input-item:first-child').parent().empty();

            });

            window.fileInventory        = {};
            window.filesLastBulk        = [];
            window.filesSizeBulk        = 0;

        }

  }

})(jQuery);

/**
 *
 *  [SOW] Count Animate
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.count_animate.init()
 *  @Ajax Support   YES
 * 
 *
 **/
;(function ($) {
    'use strict';


    /**
     *
     *  @vars
     *
     *
     **/
    var scriptInfo              = 'SOW Count Animate';
    window.sowCountInterval     = null;
    window.sowCountBtnList      = {};
    // window.startScrollPos        = 0;


    $.SOW.core.count_animate = {



        /**
         *
         *  @config
         *
         *
         **/
        config: {
            count_duration  : 1500,
            easing          : 'easeInOutExpo', // easeOutExpo, easeInOutExpo, linear, swing
        },



        /**
         *
         *  @collection
         *
         *
         **/
        collection: $(),



        /**
         *
         *  @init
         *  
         *
         **/
        init: function (selector, config) {

            var el_selector         = '[data-toggle=count]';
            var btn_selector        = '[data-count-target]';

            var __config            = $.SOW.helper.check_var(config);
            this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


            // init button
            $.SOW.core.count_animate.btn_process(el_selector, btn_selector);
            // --


            if($(el_selector).length < 1) {

                // stop everything
                if(window.sowCountInterval !== null) {
                    clearInterval(window.sowCountInterval);
                    window.sowCountInterval = null;
                    $(el_selector).stop(true, true); // stop all animations
                }

                return;
            }


            // -- * --
            $.SOW.helper.consoleLog('Init : ' + scriptInfo);
            // -- * --


            // On Load
            // window.startScrollPos = jQuery(window).scrollTop() || 0; // used on scroll up
            $.SOW.core.count_animate.process($(el_selector));



            var scrolling   = false;
            $(window).scroll(function() {
                scrolling = true;
            });

            window.sowCountInterval = setInterval( function() {

                if(scrolling) {
                    scrolling = false;
                    $.SOW.core.count_animate.process($(el_selector));
                }

            }, 250);


            // it's always cool to return something that nobody will ever need :)
            return $(el_selector);

        },





        /**
         *
         *  @process
         *  
         *
         **/
        process: function(_this) {

            // custom easing
            $.SOW.core.count_animate.custom_easing();

            var topPos      = jQuery(window).scrollTop() || 0;
            var scrollPos   = ($.SOW.globals.height || 0) + topPos;

            _this.not('.js-count-completed').each(function(e) {

                var _t              = jQuery(this),
                    _offset         = _t.offset(),
                    elOffsetTop     = _offset.top || 0;


                /**
                    
                    Known issue: refresh when scroll is on middle

                    // on scroll up (from refresh)
                    var elOffsetTop2 = elOffsetTop + $.SOW.globals.height;
                    if(window.startScrollPos > $.SOW.globals.height && scrollPos < elOffsetTop2)
                        $.SOW.core.count_animate.animate(_t);

                    // on scroll down
                    else if (window.startScrollPos < $.SOW.globals.height && scrollPos > elOffsetTop)
                        $.SOW.core.count_animate.animate(_t);

                **/

                /* on scroll down only */
                if (scrollPos > elOffsetTop)
                    $.SOW.core.count_animate.animate(_t);

            });
           
        },




        /**
         *
         *  @animate
         *  
         *
         **/
        animate: function(_t) {

            var count_from      = _t.attr('data-count-from')        || 0,
                count_to        = _t.attr('data-count-to')          || 0,
                count_duration  = _t.attr('data-count-duration')    || $.SOW.core.count_animate.count_duration,
                callback        = _t.data('count-callback')         || '',
                count_decimals  = _t.data('count-decimals')         || '',
                oncomplete      = _t.data('count-oncomplete')       || '';

            if(count_from == count_to)
                return;

            // https://api.jquery.com/animate/
            jQuery({sowCount:count_from}).animate({sowCount:count_to}, {

                duration:   Number(count_duration),
                easing:     $.SOW.core.count_animate.easing, //'linear', 'swing'
                queue:      true,

                step: function(curr) {



                    var numRes  = count_to.substr(0, count_to.indexOf('.')),
                        _val    = (numRes) ? parseFloat(curr).toFixed(2) : Math.floor(curr);

                    if(count_decimals)
                        var _val = parseFloat(_val).toFixed(count_decimals);

                    _t.text(_val);

                },

                start: function() {

                    _t.addClass('js-count-completed');

                },

                complete: function() {

                    if(callback != '' && typeof $.SOW.helper.executeFunctionByName === "function")
                        $.SOW.helper.executeFunctionByName(callback, window, _t);


                    if(typeof oncomplete === 'object') {

                        if(oncomplete.target) {

                            if(oncomplete.action.toLowerCase() == 'show')
                                jQuery(oncomplete.target).removeClass('hide hide-force');

                            else if(oncomplete.action.toLowerCase() == 'hide')
                                jQuery(oncomplete.target).addClass('hide hide-force');

                            if(oncomplete.class)
                                jQuery(oncomplete.target).addClass(oncomplete.class);

                        }
                    }

                },

                done: function(_e) {
                    _e.stop(true, true);
                }

            });

        },





        /**
         *
         *  @btn_process
         *  
         *
         **/
        btn_process: function(el_selector, btn_selector) {

            if(jQuery(btn_selector).length < 1)
                return;

            // custom easing
            $.SOW.core.count_animate.custom_easing();

            // reset for each page (ajax required)
            window.sowCountBtnList = {};

            jQuery(btn_selector).each(function() {

                var _thisBtn    = jQuery(this),
                    _action     = (_thisBtn.is('a') || _thisBtn.is('button')) ? 'click' : 'change';

                _thisBtn.on(_action, function(e) {

                    var _t          = jQuery(this),
                        _href       = _t.attr('href')                   || null,
                        _target     = _t.data('count-target')           || null,
                        _math       = _t.data('count-math')             || null;

                    if(!_target)    return;
                    if(_href)       e.preventDefault(); // links only!
                    jQuery(_target).stop(true, true);   // stop any animation
                    _t.toggleClass('active');           // mark active



                    // :: MATH
                    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
                    $.SOW.core.count_animate.btn_process_math(_t);
                    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --



                    // :: TOGGLE
                    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
                    $.SOW.core.count_animate.btn_process_toggle(_target);
                    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


                });


            });

        },





        /**
         *
         *  @btn_process_math
         *  
         *
         **/
        btn_process_math: function(_t) {

            var _target     = _t.data('count-target')           || '',
                _tMth       = jQuery(_target),
                _math       = _t.data('count-math')             || '0',
                _math       = Number(_math),
                is_select   = _t.is('select'),
                el_type     = _t.attr('type'); // radio|checkbox


            // Select
            if(is_select) {

                var _val        = _t.find(':selected').data('count-math')   || 0
                    _lastCalc   = _tMth.attr('data-count-to')               || 0,
                    _calculate  = _val;

            // Checkbox, Radio, etc
            } else {

                // if(_math < 1) 
                //  return;

                var _lastCalc   = _tMth.attr('data-count-last-math')    || 0,
                    _lastCalc   = Number(_lastCalc), // last calculation
                    _baseVal    = _tMth.attr('data-count-base-to')      || '',
                    _currVal    = _tMth.attr('data-count-to')           || 0,
                    _currVal    = Number(_currVal);

                if(_baseVal == '') {
                    var _baseVal    = _currVal,
                        _lastCalc   = _currVal;
                        _tMth.attr('data-count-base-to', _currVal);
                }

                if(el_type == 'radio')
                    var _calculate = (_t.hasClass('active') || _t.is(':checked')) ? Number(_baseVal) + _math : Number(_lastCalc) - _math;

                
                else // invert the math because 'active' class is toggled before
                    var _calculate = (_t.hasClass('active') || _t.is(':checked')) ? Number(_lastCalc) + _math : Number(_lastCalc) - _math;

            }

            _tMth.attr('data-toggle', 'count'); // just in case!
            _tMth.attr('data-count-last-math', _calculate);
            _tMth.attr('data-count-from', _lastCalc);
            _tMth.attr('data-count-to', _calculate);
            $.SOW.core.count_animate.animate(_tMth);

        },





        /**
         *
         *  @btn_process_toggle
         *  
         *
         **/
        btn_process_toggle: function(_target) {

            jQuery(_target).each(function(e) {

                var _el         = jQuery(this),
                    _json       = _el.data('count-toggle')  || '',
                    _elID       = _el.attr('id')            || '';

                if(_elID == '') {
                    _elID = 'rand_' + $.SOW.helper.randomStr(3);
                    _el.attr('id', _elID);
                }

                if(typeof _json === 'object') {

                    // in case a button is used to toggle only one value (from-to)
                    if(!_json[1])
                        _json[1] = {"from":_json[0]['to'], "to":_json[0]['from'], "duration":_json[0]['duration']};


                    if(window.sowCountBtnList[_elID] === 1) {

                        _el.attr('data-toggle', 'count');
                        _el.attr('data-count-from', (_json[1]['from'] != '0') ? _json[1]['to'] : _json[0]['from'] );
                        _el.attr('data-count-to', _json[0]['to']);
                        _el.attr('data-count-duration', _json[0]['duration']);

                        window.sowCountBtnList[_elID] = 0;

                    } else {

                        _el.attr('data-toggle', 'count');
                        _el.attr('data-count-from', _json[1]['from']);
                        _el.attr('data-count-to', _json[1]['to']);
                        _el.attr('data-count-duration', _json[1]['duration']);

                        window.sowCountBtnList[_elID] = 1;
                    }

                    $.SOW.core.count_animate.animate(_el);

                }

            });

        },





        /**
         *
         *  @custom_easing
         *  
         *
         **/
        custom_easing: function() {

            $.extend($.easing, {
                easeInOutExpo: function (x, t, b, c, d) {
                    if (t==0) return b;
                    if (t==d) return b+c;
                    if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
                    return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
                },
                easeOutExpo: function (x, t, b, c, d) {
                    return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
                },
            });

        }
    };


})(jQuery);

/**
 *
 *  [SOW] Form Advanced
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.form_advanced.init();
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo              = 'SOW Form Advanced';


  $.SOW.core.form_advanced = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {

      /* 

          1. Bulk
          @form_advanced_bulk

      */
      selector_advanced_bulk:             "a.js-form-advanced-bulk",
      advanced_bulk_selected_require:     false,

      // toast messages
      toast_pos:                          'bottom-center',
      toast_delay:                        2000,
      toast_msg_noitems:                  'No Items Selected!',



      /* 

          2. Form input numeric limit
          @form_advanced_numeric_limit

      */
      selector_advanced_numeric_limit:        "input.js-form-advanced-limit",




      /* 

          3. Form char count
          @form_advanced_char_count_down

      */
      selector_advanced_char_count_down:      "input.js-form-advanced-char-count-down, textarea.js-form-advanced-char-count-down",
      selector_advanced_char_count_up:        "input.js-form-advanced-char-count-up, textarea.js-form-advanced-char-count-up",
      selector_advanced_type_toggle:          ".btn-password-type-toggle",

    },



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      // 1. Bulk
      $.SOW.core.form_advanced.form_advanced_bulk(this.config.selector_advanced_bulk);
      
      // 2. Form input numeric limit
      $.SOW.core.form_advanced.form_advanced_numeric_limit(this.config.selector_advanced_numeric_limit);

      // 3. Form char count down
      $.SOW.core.form_advanced.form_advanced_char_count_down(this.config.selector_advanced_char_count_down);

      // 4. Form char count up
      $.SOW.core.form_advanced.form_advanced_char_count_up(this.config.selector_advanced_char_count_up);
      
      // 5. Form password toggle
      $.SOW.core.form_advanced.form_advanced_type_toggle(this.config.selector_advanced_type_toggle);

      // 6. Misc
      $.SOW.core.form_advanced.formAdvancedTableVariants(); // keep it first
      $.SOW.core.form_advanced.formatCreditCard();
      $.SOW.core.form_advanced.formAdvancedList();
      $.SOW.core.form_advanced.formAdvancedReset();
      $.SOW.core.form_advanced.formAdvancedRequired();


      // No chaining
      return null;

    },



    /**
     *
     *  @form_advanced_bulk
        Form actions[submit] using a regular link
     *
     *  
     *
     **/
    form_advanced_bulk: function(_this) {

      // -- * --
      if(jQuery(_this).length > 0)
          $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --

      jQuery(_this).not('.js-form-advancified').addClass('js-form-advancified').on('click', function(e) {
        e.preventDefault();

        var _t                          = jQuery(this),
            
            _citems                     = _t.data('js-form-advanced-bulk-container-items')                      || 'table tbody',       // from what container to count checked items?
            _reqSelectedItems           = _t.data('js-form-advanced-bulk-required-selected')                    || $.SOW.core.form_advanced.config.advanced_bulk_selected_require,
            _requiredMsg                = _t.data('js-form-advanced-bulk-required-txt-error')                   || $.SOW.core.form_advanced.config.toast_msg_noitems,
            _requiredMsgPos             = _t.data('js-form-advanced-bulk-required-txt-position')                || $.SOW.core.form_advanced.config.toast_pos,       // button icon

            // Modal
            _reqModalCustom             = _t.data('js-form-advanced-bulk-required-custom-modal')                || '', // modal id
            _reqModalCustomAjaxUrl      = _t.data('js-form-advanced-bulk-required-custom-modal-content-ajax')   || '',

            _reqModalType               = _t.data('js-form-advanced-bulk-required-modal-type')                  || 'secondary',         // 'custom' : In case we want an ajax modal, fully customizable (no header/body/footer)
            _reqModalSize               = _t.data('js-form-advanced-bulk-required-modal-size')                  || 'modal-md',
            _reqModalBackdrop           = _t.data('js-form-advanced-bulk-required-modal-backdrop')              || '',          // static
            _reqModalTitle              = _t.data('js-form-advanced-bulk-required-modal-txt-title')             || 'Please Confirm',
            _reqModalTxtSubtitle        = _t.data('js-form-advanced-bulk-required-modal-txt-subtitle')          || '-',
            _reqModalTxtBodyTxt         = _t.data('js-form-advanced-bulk-required-modal-txt-body-txt')          || 'Are you sure?',
            _reqModalTxtBodyInfo        = _t.data('js-form-advanced-bulk-required-modal-txt-body-info')         || '',
            _reqModalBtnTxtYes          = _t.data('js-form-advanced-bulk-required-modal-btn-text-yes')          || 'Submit', 
            _reqModalBtnTxtNo           = _t.data('js-form-advanced-bulk-required-modal-btn-text-no')           || 'Cancel',
            _reqModalBtnIcoYes          = _t.data('js-form-advanced-bulk-required-modal-btn-icon-yes')          || $.SOW.config.sow__icon_check,        // button icon
            _reqModalBtnIcoNo           = _t.data('js-form-advanced-bulk-required-modal-btn-icon-no')           || $.SOW.config.sow__icon_close,        // button icon

            // Form
            _formID                     = _t.attr('data-js-form-advanced-form-id')                              || '', // form#id .attr REQUIRED, or old one used!
            _formSubmitNoConfirm        = _t.data('js-form-advanced-bulk-submit-without-confirmation')          || 'false',

            // Hidden action input
            _formActionID               = _t.data('js-form-advanced-bulk-hidden-action-id')                     || '#action',
            _formActionVal              = _t.data('js-form-advanced-bulk-hidden-action-value')                  || '';

        // count selecteditems & update
        var total_selected_items = jQuery(_citems + " input:checked").length;



        // Check for required selected items
        if(_reqSelectedItems == true && Number(total_selected_items) < 1) {

          // SHOW ERROR
          if(typeof $.SOW.core.toast === 'object') {
              $.SOW.core.toast.destroy();
              $.SOW.core.toast.show('danger', '', _requiredMsg, _requiredMsgPos, $.SOW.core.form_advanced.config.toast_delay, true);
          } else {
              alert(_requiredMsg);
          }

          e.stopPropagation();
          return;

        }






        // Update action hidden input
        if(_formActionVal != '')
          jQuery(_formActionID).val(_formActionVal);


        // Direct submit, no confirmation
        if(_formSubmitNoConfirm != 'false') {
          jQuery(_formID).unbind().submit(); // unbind required
          return;
        }


        // Button Icons
        if(_reqModalBtnIcoYes.length > 1)
          var _reqModalBtnTxtYes = '<i class="' + _reqModalBtnIcoYes + '"></i> ' + _reqModalBtnTxtYes;

        if(_reqModalBtnIcoNo.length > 1)
          var _reqModalBtnTxtNo = '<i class="' + _reqModalBtnIcoNo + '"></i> ' + _reqModalBtnTxtNo;


        // Replacements
        _reqModalTitle          = _reqModalTitle.replace('{{no_selected}}', total_selected_items);
        _reqModalTxtBodyTxt     = _reqModalTxtBodyTxt.replace('{{no_selected}}', total_selected_items);
        _reqModalTxtBodyInfo    = _reqModalTxtBodyInfo.replace('{{no_selected}}', total_selected_items);
        _reqModalTxtSubtitle    = _reqModalTxtSubtitle.replace('{{no_selected}}', total_selected_items);


        // Additional info
        var _reqModalTxtBodyTxt = (_reqModalTxtBodyInfo != '') ? _reqModalTxtBodyTxt + '<span class="d-block d-block small mt-1">' + _reqModalTxtBodyInfo + '</span>' : _reqModalTxtBodyTxt;








        // 1. Inline Modal
        if(_reqModalCustom != '') {

          // Update selected items counter
          if(_reqModalCustom != '')
            $.SOW.core.form_advanced.form_advanced_bulk_counter_update(total_selected_items);


          // SHOW MODAL
          jQuery(_reqModalCustom).modal('show');


          // LOAD FROM AJAX
          if(_reqModalCustomAjaxUrl != '') {
            
            jQuery(_reqModalCustom).find('.modal-content').load(_reqModalCustomAjaxUrl, function() {

              // Update selected items counter
              $.SOW.core.form_advanced.form_advanced_bulk_counter_update(total_selected_items);

            });

          }

          // stop here
          return;
        
        } 



                                

        // 2. Generated modal (regular type)
        var _tpl = '<div class="modal fade" id="js_advanced_form_bulk_modal_confirm" role="dialog" tabindex="-1" aria-labelledby="modal-title-confirm" aria-hidden="true" data-backdrop="'+_reqModalBackdrop+'">'
            + '<div class="modal-dialog '+_reqModalSize+'" role="document">'

                + '<div class="modal-content">'

                    + '<div class="modal-header border-0 bg-'+_reqModalType+'-soft">'
                        
                        + '<h5 id="modal-title-confirm" class="modal-title font-light line-height-1">'
                            + _reqModalTitle
                            + '<small class="d-block mt-1" style="font-size:13px">'+_reqModalTxtSubtitle+'</small>'
                        + '</h5>'

                        + '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'

                    + '</div>'

                    + '<div class="modal-body p-lg-4">'

                            + _reqModalTxtBodyTxt

                    + '</div>'

                    + '<div class="modal-footer border-0">'

                        + '<button type="submit" class="btn btn-js-advanced-form-bulk-confirm-yes btn-'+ _reqModalType +'">'
                            + _reqModalBtnTxtYes
                        + '</button>'

                        + '<a href="#" class="btn btn-js-advanced-form-bulk-confirm-no btn-light" data-bs-dismiss="modal">'
                            + _reqModalBtnTxtNo
                        + '</a>'

                    + '</div>'

                + '</div>'

            + '</div>'
        + '</div>';


        // In case we want an ajax modal, fully customizable (no header/body/footer)
        var _tplCustom = '<div class="modal fade show" id="js_advanced_form_bulk_modal_confirm" role="dialog" tabindex="-1" aria-labelledby="modal-title-confirm" aria-hidden="true" data-backdrop="'+_reqModalBackdrop+'">'
            + '<div class="modal-dialog '+_reqModalSize+'" role="document">'
                + '<div class="modal-content"></div>'
            + '</div>'
        + '</div>';


        // which template we use?
        var _modalBody = '#js_advanced_form_bulk_modal_confirm .modal-body';
        if(_reqModalType == 'custom') {

          var _tpl        = _tplCustom,
              _modalBody  = '#js_advanced_form_bulk_modal_confirm .modal-content';
      
        }



        // Add modal to DOM
        jQuery('#js_advanced_form_bulk_modal_confirm').remove();
        jQuery(_formID).append(_tpl); // REQUIRED INSIDE THE FORM! Because of Submit button!

        // Show Modal
        jQuery('#js_advanced_form_bulk_modal_confirm').modal('handleUpdate').modal('show');

        // Modal Backdrop fix (Smarty v3.0.9)
        if(jQuery('.modal-backdrop').length < 1) {

          $.SOW.globals.elBody.append('<div class="modal-backdrop fade show"></div>');

          jQuery('#js_advanced_form_bulk_modal_confirm').on('hidden.bs.modal', function (e) {
            jQuery('.modal-backdrop').remove();
          });

        } else {

          jQuery('.modal-backdrop').addClass('show');

          jQuery('#js_advanced_form_bulk_modal_confirm').on('hidden.bs.modal', function (e) {
            jQuery('.modal-backdrop').removeClass('show');
          });

        }

        // Custom Ajax Content
        if(_reqModalCustomAjaxUrl != '') {
            
          jQuery(_modalBody).empty().append('<div class="py-4 text-center animate-bouncein"><i class="'+$.SOW.config.sow__icon_loading+' fs-1 text-muted"></i></div>');

          jQuery(_modalBody).load(_reqModalCustomAjaxUrl, function() {

            // Update selected items counter
            $.SOW.core.form_advanced.form_advanced_bulk_counter_update(total_selected_items);

            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
            if(_reqModalType == 'custom') {
                setTimeout(function() {

                    // console log
                    $.SOW.helper.consoleLogReinit(scriptInfo, _modalBody);
                    // reinit inside ajax container
                    $.SOW.reinit(_modalBody);

                }, 200);
            }
            // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


          });

        }

        return;

      });

    },




    /**
     *
     *  @form_advanced_bulk_counter_update
     *  :: Helper
     *  
     *
     **/
    form_advanced_bulk_counter_update: function(total_selected_items) {

      // Update selected items
      jQuery('.js-form-advanced-selected-items').html(total_selected_items);

    },


    /**
     *
     *  @form_advanced_numeric_limit
        Input min/max limits

            <!-- input limit + hidden message -->
            <div class="position-relative">

                <span class="js-form-advanced-limit-info badge bg-warning hide animate-bouncein position-absolute absolute-top m--2">
                    please, order between 1 and 48.
                </span>

                <input type="number" value="8" min="1" max="48" class="form-control js-form-advanced-limit">
            </div>
            <!-- /input limit + hidden message -->

     *
     *  
     *
     **/
    form_advanced_numeric_limit: function(_this) {

      var __interval__ = null;
      document.querySelectorAll( _this ).forEach(function(el) {

          el.addEventListener('keyup', function(e) {

            var _min = e.target.getAttribute('min') || '',
                _max = e.target.getAttribute('max') || '';

            if( __interval__ )
              clearInterval( __interval__ );

            // wait 5 seconds for empty value
            // then update with minimum
            if( _min != '' && e.target.value == '' ) {

              __interval__ = setInterval(function() {

                if(e.target.value == '')
                  e.target.value = _min;

              }, 5000);

              return;

            }

            // min allowed
            if( _min != '' && Number(e.target.value) < Number(_min) )
              e.target.value = _min;

            // max allowed
            if( _max != '' && Number(e.target.value) > Number(_max) ) {
              e.target.value = _max;

              // Optional simple info message
              $.SOW.core.form_advanced.form_advanced_simple_alert( jQuery( el ) );
            }

          });

      });

    },



    /**
     *
     *  @form_advanced_char_count_down
        Char Count Down

        <!-- input -->
        <div class="position-relative">
            
            <span class="js-form-advanced-limit-info badge bg-warning hide animate-bouncein position-absolute absolute-top m--2">
                100 chars limit
            </span>

            <input type="text" name="-" class="form-control js-form-advanced-char-count-down" data-output-target=".js-form-advanced-char-left" value="" maxlength="100">
            
            <div class="small text-muted text-align--end mt--3">
                characters left: <span class="js-form-advanced-char-left">100</span>
            </div>
        
        </div>
        
        <br>

        <!-- textarea -->
        <div class="position-relative">
            
            <span class="js-form-advanced-limit-info badge bg-warning hide animate-bouncein position-absolute absolute-top m--2">
                100 chars limit
            </span>

            <textarea class="js-form-advanced-char-count-down form-control" data-output-target=".js-form-advanced-char-left2" maxlength="100"></textarea>
            
            <div class="small text-muted text-align--end mt--3">
                characters left: <span class="js-form-advanced-char-left2">100</span>
            </div>

        </div>

     *
     *  
     *
     **/
    form_advanced_char_count_down: function(_this) {


      if(jQuery(_this).length < 1)
          return;

      jQuery(_this).keyup(function(e) {

        var _t      = jQuery(this),
            _val    = _t.val(),
            _length = _val.length,
            _max    = _t.attr('maxlength')      || 0,
            _output = _t.data('output-target')  || '.char-left';

        if(_max < 1 && _output != '')
          return;

        if(_length >= _max) {
         
          _t.val(_val.substring(0, _max - 1)); // limit - remove anything over maximum allowed
          jQuery(_output).html('0');

          // Optional simple info message
          $.SOW.core.form_advanced.form_advanced_simple_alert(_t);

        } else {
          var _left = _max - _length;
          jQuery(_output).html(_left);
        }


      });

    },





    /**
     *
     *  @form_advanced_char_count_up
        Char Count Up


        Remove maxlength for no limit, only to count chars



        <!-- input -->
        <div class="position-relative">
            
            <span class="js-form-advanced-limit-info badge bg-warning hide animate-bouncein position-absolute absolute-top m--2">
                100 chars limit
            </span>

            <input type="text" name="-" class="form-control js-form-advanced-char-count-up" data-output-target=".js-form-advanced-char-total" value="" maxlength="100">
            
            <div class="small text-muted text-align--end mt--3">
                characters: <span class="js-form-advanced-char-total">0</span> / 100
            </div>

        </div>

        <br>

        <!-- textarea -->
        <div class="position-relative">
            
            <span class="js-form-advanced-limit-info badge bg-warning hide animate-bouncein position-absolute absolute-top m--2">
                100 chars limit
            </span>

            <textarea class="js-form-advanced-char-count-up form-control" data-output-target=".js-form-advanced-char-total2" maxlength="100"></textarea>
            
            <div class="small text-muted text-align--end mt--3">
                characters: <span class="js-form-advanced-char-total2">0</span> / 100
            </div>

        </div>

     *
     *  
     *
     **/
    form_advanced_char_count_up: function(_this) {

      if(jQuery(_this).length < 1)
        return;

      jQuery(_this).keyup(function() {

        var _t      = jQuery(this),
            _val    = _t.val(),
            _length = _val.length               || 0,
            _max    = _t.attr('maxlength')      || 0,
            _output = _t.data('output-target')  || '.char-count';

        jQuery(_output).html(_length);

        // limit if specified
        if(_length >= _max && _max > 0) {
          _t.val(_val.substring(0, _max)); // limit - remove anything over maximum allowed

          // Optional simple info message
          $.SOW.core.form_advanced.form_advanced_simple_alert(_t);
        }

      });

    },




    /**
     *
     *  @form_advanced_simple_alert
     *  Optional form alert
     *  
     *
     **/
    form_advanced_simple_alert: function(_this) {

      _this.next('.js-form-advanced-limit-info').removeClass('hide');
      _this.prev('.js-form-advanced-limit-info').removeClass('hide');

      setTimeout(function() {
          _this.next('.js-form-advanced-limit-info').addClass('hide');
          _this.prev('.js-form-advanced-limit-info').addClass('hide');
      }, 3000);

    },




    /**
     *
     *  @form_advanced_type_toggle
     *  
     *
     **/
    form_advanced_type_toggle: function(_this) {

      if(jQuery(_this).length < 1)
        return;

      jQuery(_this).not('.js-form_advanced_type_toggle').addClass('js-form_advanced_type_toggle').on('click', function(e) {
        e.preventDefault();

        var _target = jQuery(this).data('target') || jQuery(this).data('bs-target') || '';
        if(_target == '') return;

        jQuery(this).toggleClass('active');
        if(jQuery(_target).attr('type') == 'password') {
            jQuery(_target).attr('type', 'text');
        } else {
            jQuery(_target).attr('type', 'password');
        }

      });

    },




    /**
     *
     *  @formAdvancedList
     *  on a list, reveal/expand selected (example: payment method - checkout)
     *
     **/
    formAdvancedList: function() {

      jQuery('.form-advanced-list').each(function() {

          var _t                      = jQuery(this),
              _listReavealCount       = jQuery('.form-advanced-list-reveal', _t).length,
              _formValidate           = _t.parents('form.bs-validate'),
              _mainForm               = _formValidate                                             || _t.parents('form'),
              _isFormValidate         = (_formValidate.length > 0) ? true : false,
              _disableRequired        = _t.attr('data-form-advanced-list-hidden-required')        || 'false',
              _disableHidden          = _t.attr('data-form-advanced-list-hidden-disable')         || 'false',
              _disableHiddenByClass   = _t.attr('data-form-advanced-list-hidden-disable-class')   || '';


          // inside validation form
          // "unrequire" all first!
          // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
          // if(_disableRequired == 'true' && _isFormValidate > 0 && _listReavealCount > 0) {
          if(_disableRequired == 'true' && _listReavealCount > 0) {

              if(_disableHidden == 'true') {
                  jQuery('.form-advanced-list-reveal-item [disabled]', _t).not('.js-form-advanced-list-ignore').addClass('js-form-advanced-disabled');
                  jQuery('.form-advanced-list-reveal-item:hidden [disabled]', _t).addClass('js-form-advanced-disabled').not('.js-form-advanced-list-ignore').prop('disabled', false);
              }

              if(_disableHiddenByClass != '') {
                  jQuery('.form-advanced-list-reveal-item '+_disableHiddenByClass, _t).not('.js-form-advanced-list-ignore').addClass('js-form-advanced-disabled-class');
                  jQuery('.form-advanced-list-reveal-item:hidden '+_disableHiddenByClass, _t).addClass('js-form-advanced-disabled-class').not('.js-form-advanced-list-ignore').prop('disabled', false);
              }

              jQuery('.form-advanced-list-reveal-item [required]', _t).addClass('js-form-advanced-required');
              jQuery('.form-advanced-list-reveal-item:hidden [required]', _t).addClass('js-form-advanced-required').prop('required', false);

          }
          // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++





          // Each Option
          jQuery('.form-advanced-list-reveal', _t).on('change', function() {

              var __t         = jQuery(this),
                  __targetEl  = __t.data('form-advanced-target')              || '',
                  __target    = jQuery(__targetEl)                            || '',
                  __ajax      = __t.data('form-advanced-ajax-url')            || '',
                  __ajaxIcon  = __t.attr('data-form-advanced-ajax-icon')      || 'true';

              // hide all first
              jQuery('.form-advanced-list-reveal-item', _t).addClass('hide hide-force');


              // reveal selected
              if(__target != '') {

                  // show content
                  __target.removeClass('hide hide-force');


                  // load ajax content
                  // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                  if(__ajax != '' && !__target.hasClass('js-ajaxified')) {


                      // loading icon
                      if(__ajaxIcon == true)
                          __target.empty().append('<div class="py-4 text-center animate-bouncein"><i class="'+$.SOW.config.sow__icon_loading+' fs--40 text-muted"></i></div>');
                      // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


                      // ajax
                      jQuery(__target).load(__ajax, function() {

                          // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
                          setTimeout(function() {

                              // console log
                              var __for = __target.attr('id');
                              $.SOW.helper.consoleLogReinit(scriptInfo, __for);

                              // Initial Set
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                              jQuery('.form-advanced-list-reveal-item [required]', _t).addClass('js-form-advanced-required');
                              jQuery('.form-advanced-list-reveal-item:hidden [required]', _t).addClass('js-form-advanced-required').prop('required', false);
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


                              // Disable hidden elements by class
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                              if(_disableHiddenByClass != '') {
                                  jQuery('.form-advanced-list-reveal-item:visible '+_disableHiddenByClass, _t).not('.js-form-advanced-list-ignore').prop('disabled', false);
                                  jQuery('.js-form-advanced-disabled-class:hidden'+_disableHiddenByClass, __target).not('.js-form-advanced-list-ignore').prop('disabled', true);
                              }
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

                              // Show Elements
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                              jQuery('.form-advanced-list-reveal-item:hidden [required]', _t).prop('required', false);
                              jQuery('.js-form-advanced-required:visible', __target).prop('required', true);
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



                              // reinit plugins
                              $.SOW.reinit(__target.attr('id'));
                              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


                          }, 400);
                          // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

                      }).addClass('js-ajaxified');

                  } 
                  // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

              }







              // inside validation form
              // remove quired when hidden
              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
              // if(__target != '' && _disableRequired == 'true' && _isFormValidate > 0) {
              if(__target != '' && _disableRequired == 'true') {
                  
                  // Disable hidden elements
                  if(_disableHidden+'' == 'true') {
                      jQuery('.form-advanced-list-reveal-item:hidden [disabled]', _t).not('.js-form-advanced-list-ignore').prop('disabled', false);
                      jQuery('.js-form-advanced-disabled:visible', __target).not('.js-form-advanced-list-ignore').prop('disabled', true);
                  }

                  // Disable hidden elements by class
                  if(_disableHiddenByClass != '') {
                      jQuery('.form-advanced-list-reveal-item:visible '+_disableHiddenByClass, _t).not('.js-form-advanced-list-ignore').prop('disabled', false);
                      jQuery('.js-form-advanced-disabled-class:hidden'+_disableHiddenByClass, __target).not('.js-form-advanced-list-ignore').prop('disabled', true);
                  }

                  // Show Elements
                  jQuery('.form-advanced-list-reveal-item:hidden [required]', _t).prop('required', false);
                  jQuery('.js-form-advanced-required:visible', __target).prop('required', true);

              }
              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


              // hide validate errors
              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
              jQuery('.bs-validate-info').addClass('hide hide-force');
              _mainForm.removeClass('was-validated');
              // alert(_mainForm[0].checkValidity());
              // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

          });

      });

    },





    /**
     *
     *  @formAdvancedReset
     *  reset form on button click
     *
     **/
    formAdvancedReset: function() {

      jQuery('a.form-advanced-reset, button.form-advanced-reset').not('.js-advancified').addClass('js-advancified').each(function(e) {

          var _t          = jQuery(this),
              _target     = _t.data('target-reset')           || _t.attr('href'),
              _exclude    = _t.data('exclude-reset')          || '';

          if(_target == '')
              return null;

          jQuery(_target+' input').on('change', function() {
              _t.not(_exclude).removeClass('hide hide-force');
          });

          jQuery(_target+' textarea').on('change', function() {
              _t.not(_exclude).removeClass('hide hide-force');
          });

          // reset button click
          _t.on('click', function(e) {
              e.preventDefault();

              // checkboxes & radios
              // do NOT reset value!
              jQuery(_target+' input').each(function(el) {
                  var thisEl      = jQuery(this),
                      thisType    = thisEl.attr('type').toLowerCase();

                  if( thisType != 'checkbox' && thisType != 'radio' ) {
                      thisEl.not(_exclude).val('');
                  }

              });
              
              jQuery(_target+' textarea').not(_exclude).val('');
              jQuery(_target+' input[type=checkbox]').not(_exclude).prop('checked', false);
              jQuery(_target+' input[type=radio]').not(_exclude).prop('checked', false);

              // hide reset button
              if(!_t.hasClass('js-ignore'))
                  _t.addClass('hide hide-force');

          });


      });

    },




    /**
     *
     *  @formAdvancedRequired
     *  manage required attribute on hidden
     *
     **/
    formAdvancedRequired: function() {

      jQuery('div.js-form-advanced-required').not('.js-advancrequirefied').addClass('js-advancrequirefied').each(function(e) {

        var _t    = jQuery(this),
            _elID = _t.attr('id') || '';
        
        if(_elID == '') return null;

        // 1. add identifier class for each required element
        jQuery('[required]', _t).each(function() {
          jQuery(this).addClass('js-required-hidden');
        });

        // 2. remove required attribute for hidden elements
        if(_t.is(':hidden')) {
          jQuery('.js-required-hidden', _t).prop('required', false);
        }

        // 3. add|remove required attribute 
        jQuery('input[data-target="#'+_elID+'"], a[href="#'+_elID+'"], .js-form-advanced-required-toggler').on('click', function(e) {
          $.SOW.core.form_advanced.__switchFormAdvancedRequired();
        });
        jQuery('select.js-form-advanced-required-toggler').on('change', function(e) {
          $.SOW.core.form_advanced.__switchFormAdvancedRequired();
        });

      });

    },
        __switchFormAdvancedRequired: function() {

          window.setTimeout(function() {

            jQuery('div.js-form-advanced-required').each(function() {

              var _container = jQuery(this);

              if(_container.is(':hidden')) {
                jQuery('.js-required-hidden', _container).prop('required', false);
              } else {
                jQuery('.js-required-hidden', _container).prop('required', true);
              }

            });
          
          }, 450);

        },



    /**
     *
     *  @formAdvancedTableVariants
     *  generate columns
     *
     **/
    formAdvancedTableVariants: function() {

        jQuery('div.js-form-advanced-table').not('.js-advtablified').addClass('js-advtablified').each(function(e) {

            var _t              = jQuery(this),
                _elID           = _t.attr('id') || '',
                _columnBefore   = _t.data('table-column-insert-before')     || 'tr>td:first-child',
                _columnElement  = _t.data('table-column-insert-element')    || '<input type="text" class="form-control form-control-sm" value="">',
                _columnDelBtn   = _t.data('table-column-delete-button')     || '<span class="btn-table-column-delete fi fi-close small cursor-pointer px-1 d-inline-block"></span>',
                _columnLimit    = _t.data('table-column-limit')             || 0,
                _cloneLimit     = _t.data('table-row-limit')                || 0,
                _cloneMethod    = _t.data('table-row-method')               || 'append';    // append|prepend

            // Assign a random id if not exist
            if(_elID == '') {
              var _elID = 'js_' + $.SOW.helper.randomStr(10);
              _t.attr('id', _elID);
            }

            /* Create clone of frist TR on load! */
            var _clone = jQuery('table>tbody>tr:first-child', _t).clone();
                _clone.addClass('js-cloned').removeClass('js-ignore hide hide-force');

            // remove first TR, if is hidden (acting as a template)
            if(jQuery('table>tbody>tr:first-child', _t).hasClass('hide'))
              jQuery('table>tbody>tr:first-child', _t).remove();
                

            // resets
            jQuery('input, textarea', _clone).val('').removeClass('js-tangepickified js-rangepickified js-bselectified js-datepickified js-advancified');
            jQuery('a.btn-table-clone', _clone).removeClass('btn-table-clone btn-primary btn-danger btn-secondary').addClass('btn-table-clone-remove btn-light').attr('aria-expanded','true');
            // reset : file upload, if has items
            if(jQuery('input.custom-file-input', _clone).length > 0) {

              var _fileUploadPrevContainer    = jQuery('input.custom-file-input', _clone).attr('data-file-preview-container'),
                  _fileUploadRemoveButton     = jQuery('input.custom-file-input', _clone).attr('data-file-btn-clear');

              jQuery(_fileUploadPrevContainer, _clone).empty();
              jQuery(_fileUploadRemoveButton, _clone).addClass('hide');
              jQuery('input.custom-file-input', _clone).prop('disabled', false);

            }


            /* CLONE BUTTON */
            jQuery('a.btn-table-clone', _t).on('click', function(e) {
              e.preventDefault();


              // generate id
              var _cloneID = 'clone_' + $.SOW.helper.randomStr(6);
              _clone.attr('id', _cloneID);

              // file upload
              if(jQuery('input.custom-file-input', _clone).length > 0) {
                var _fileUploadPrevContainer    = jQuery('input.custom-file-input', _clone).attr('data-file-preview-container'),
                    _fileUploadPrevContainer    = _fileUploadPrevContainer.replace('.', '');
                var _fileUploadRemoveButton     = jQuery('input.custom-file-input', _clone).attr('data-file-btn-clear'),
                    _fileUploadRemoveButton     = _fileUploadRemoveButton.replace('.', '');

                jQuery('input.custom-file-input', _clone).attr('data-file-preview-container', '.'+_fileUploadPrevContainer+'_'+_cloneID);
                jQuery('input.custom-file-input', _clone).attr('data-file-btn-clear', '.'+_fileUploadRemoveButton+'_'+_cloneID);
                jQuery('.'+_fileUploadPrevContainer, _clone).removeClass(_fileUploadPrevContainer).addClass(_fileUploadPrevContainer+'_'+_cloneID);
                jQuery('.'+_fileUploadRemoveButton, _clone).removeClass(_fileUploadRemoveButton).addClass(_fileUploadRemoveButton+'_'+_cloneID);
              }


              // add clonned
              // jQuery('table>tbody', _t).append(_clone);
              if(_cloneMethod == 'append') {
                _clone.clone().appendTo('#'+_elID+' table>tbody');
              } else {
                _clone.clone().prependTo('#'+_elID+' table>tbody');

              }


              // remove button
              __rowDel();

              // reinits
              _t.removeClass('js-advancrequirefied');
              $.SOW.reinit('#'+_cloneID);



              // limit cloned elements
              if(Number(_cloneLimit) > 0) {
                if(jQuery('table>tbody>tr', _t).length >= Number(_cloneLimit)) {
                  jQuery('.btn-table-clone', _t).addClass('disabled').prop('disabled', true);
                }
              }

            });




            /* COLUMN ADD */
            jQuery('.js-form-advanced-table-column-add button', _t).on('click', function(e) {
                e.preventDefault();

                var _tc         = jQuery(this),
                    _el         = _tc.parents('.js-form-advanced-table-column-add'),
                    
                    _columnName = jQuery('input', _el).val()                || '',
                    _columnName = _columnName.trim(),
                    
                    _optionName = jQuery('input', _el).attr('name')         || '',
                    _optionName = _optionName.trim();

                // check if empty
                jQuery('input', _el).removeClass('is-invalid');
                if(_columnName == '') {
                    jQuery('input', _el).addClass('is-invalid');
                    window.setTimeout(function() {
                        jQuery('input', _el).removeClass('is-invalid');
                    }, 1000);
                    return;
                }

                // create input name
                var _optionName = (_optionName == '') ? _columnName : _optionName+'['+_columnName+']',
                    _optionName = _optionName.toLowerCase();
                var _colID = 'js_' + $.SOW.helper.randomStr(6);
                var _columnNameUcFirst = _columnName.replace(/^./, _columnName[0].toUpperCase()); 


                // check if already exist
                if(jQuery('tbody [name="'+_optionName+'[]"]', _t).length > 0) {
                    jQuery('input', _el).val('');
                    return null;
                }


                // Add new thead
                var _newTH = '<th data-id="'+_colID+'" class="js-table-option">'+_columnDelBtn+_columnNameUcFirst+'</th>';
                jQuery('thead th'+_columnBefore, _t).before(_newTH);

                // Add new body
                var _rowEl = jQuery('<div>' + _columnElement + '</div>');
                    _rowEl.find('*').attr('name', _optionName+'[]'); // shoul be array
                var _newTD = '<td class="js-table-option '+_colID+'">'+_rowEl.html()+'</td>';
                jQuery('tbody tr>td'+_columnBefore, _t).before(_newTD);

                // add column to _colne
                jQuery('td'+_columnBefore, _clone).before(_newTD);

                // remove column button
                jQuery('thead th[data-id='+_colID+'] .btn-table-column-delete', _t).on('click', function(e) {
                    e.preventDefault();

                    var _colIDDel = jQuery(this).parents('th').data('id');

                    // remove column to _colne
                    jQuery('.'+_colIDDel, _clone).remove();

                    // remove from table
                    jQuery('.'+_colIDDel, _t).remove();
                    jQuery('th[data-id='+_colIDDel+']', _t).remove();

                    // limit cloned elements
                    if(Number(_columnLimit) > 0) {
                        if(jQuery('thead th.js-table-option', _t).length < Number(_columnLimit)) {
                            jQuery('.js-form-advanced-table-column-add button', _t).removeClass('disabled').prop('disabled', false);
                            jQuery('.js-form-advanced-table-column-add input', _t).prop('disabled', false);
                        }
                    }

                });

                // reinits
                _t.removeClass('js-advancrequirefied');
                $.SOW.reinit('td.'+_colID);

                // reset field
                jQuery('input', _el).val('');


                // limit cloned elements
                if(Number(_columnLimit) > 0) {
                    if(jQuery('thead th.js-table-option', _t).length >= Number(_columnLimit)) {
                        jQuery('.js-form-advanced-table-column-add button', _t).addClass('disabled').prop('disabled', true);
                        jQuery('.js-form-advanced-table-column-add input', _t).prop('disabled', true);
                    }
                }

            });




            /*

                ON LOAD

            */




            /* ON LOAD : COLUMN REMOVE */
            jQuery('thead th.js-table-option', _t).each(function() {

                var _tcol = jQuery(this);

                // add delete button
                _tcol.prepend(_columnDelBtn);

                // remove column button
                jQuery('.btn-table-column-delete', _tcol).on('click', function(e) {
                    e.preventDefault();

                    var _colIDDel = jQuery(this).parents('th').data('id');

                    // remove column to _colne
                    jQuery('.'+_colIDDel, _clone).remove();

                    // remove from table
                    jQuery('.'+_colIDDel, _t).remove();
                    jQuery('th[data-id='+_colIDDel+']', _t).remove();

                    // limit cloned elements
                    if(Number(_columnLimit) > 0) {
                        if(jQuery('thead th.js-table-option', _t).length < Number(_columnLimit)) {
                            jQuery('.js-form-advanced-table-column-add button', _t).removeClass('disabled').prop('disabled', false);
                            jQuery('.js-form-advanced-table-column-add input', _t).prop('disabled', false);
                        }
                    }

                });

            });


            /* ON LOAD : ROW REMOVE */
            function __rowDel() {

                jQuery('table>tbody>tr>td a.btn-table-clone-remove', _t).off().on('click', function(e) {
                    e.preventDefault();

                    // remove element
                    jQuery(this).parents('tr').remove();

                    // enable clone button
                    if(Number(_cloneLimit) > 0) {
                        if(jQuery('table>tbody>tr', _t).length < Number(_cloneLimit)) {
                            jQuery('.btn-table-clone', _t).removeClass('disabled').prop('disabled', false);
                        }
                    }

                });

                // confirm
                jQuery('table>tbody>tr>td .btn-table-clone-remove-confirm', _t).off().on('click', function(e) {
                    e.preventDefault();

                    jQuery(this).parent().find('div').removeClass('hide hide-force');
                });

                // confirm cancel
                jQuery('table>tbody>tr>td .btn-table-clone-remove-cancel', _t).off().on('click', function(e) {
                    e.preventDefault();

                    jQuery(this).parents('td').find('div').addClass('hide hide-force');
                });

            }   __rowDel();


        });

    },






    /**
     *
     *  @formatCreditCard
     *  
     *
     **/
    formatCreditCard: function() {


        /*

            Credit card number

        */
        jQuery('input.cc-format.cc-number').keyup(function() {

            var _t              = jQuery(this),
                val             = _t.val() || '',
                targetCardType  = _t.data('card-type') || '';

            // format
            var cc_formatted = $.SOW.core.form_advanced.formatCardNumber(val);
            _t.val(cc_formatted);

            // Credit card type
            if(targetCardType != '') {
                var cc_type = $.SOW.core.form_advanced.detectCardType(val);
                var cc_type = (cc_type) ? cc_type.name : '';
                jQuery(targetCardType).val(cc_type);
            }

        });


        /*

            Credit card expire

        */
        jQuery('input.cc-format.cc-expire').keyup(function(e) {

            var _t              = jQuery(this),
                val             = _t.val() || '',
                code            = e.keyCode,
                allowedKeys     = [8];

            if(allowedKeys.indexOf(code) !== -1) {
                return;
            }

            e.target.value = e.target.value.replace(
                /^([1-9]\/|[2-9])$/g, '0$1/' // 3 > 03/
                ).replace(
                /^(0[1-9]|1[0-2])$/g, '$1/' // 11 > 11/
                ).replace(
                /^([0-1])([3-9])$/g, '0$1/$2' // 13 > 01/3
                ).replace(
                /^(0?[1-9]|1[0-2])([0-9]{2})$/g, '$1/$2' // 141 > 01/41
                ).replace(
                /^([0]+)\/|[0]+$/g, '0' // 0/ > 0 and 00 > 0
                ).replace(
                /[^\d\/]|^[\/]*$/g, '' // To allow only digits and `/`
                ).replace(
                /\/\//g, '/' // Prevent entering more than 1 `/`
            );

        });

    },



    /**
     *
     *  @formatCardNumber
     *  https://www.peterbe.com/plog/cc-formatter
     *
     **/
    formatCardNumber: function(value) {

        var v       = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        var matches = v.match(/\d{4,16}/g);
        var match   = matches && matches[0] || '';
        var parts   = [];
        
        for (var i=0, len=match.length; i<len; i+=4) {
            parts.push(match.substring(i, i+4));
        }

        if(parts.length) {
            return parts.join(' ');
        } else {
            return value;
        }

    },




    /**
     *
     *  @detectCardType
     *  https://www.paypalobjects.com/en_GB/vhelp/paypalmanager_help/credit_card_numbers.htm
     *
     **/
    detectCardType: function(number) {

        var card_types = [
          {
            name: 'amex',
            pattern: /^3[47]/,
            valid_length: [15]
          }, {
            // name: 'diners_club_carte_blanche',
            name: 'diners',
            pattern: /^30[0-5]/,
            valid_length: [14]
          }, {
            // name: 'diners_club_international',
            name: 'diners',
            pattern: /^36/,
            valid_length: [14]
          }, {
            name: 'jcb',
            pattern: /^35(2[89]|[3-8][0-9])/,
            valid_length: [16]
          }, {
            name: 'laser',
            pattern: /^(6304|670[69]|6771)/,
            valid_length: [16, 17, 18, 19]
          }, {
            // name: 'visa_electron',
            name: 'visa',
            pattern: /^(4026|417500|4508|4844|491(3|7))/,
            valid_length: [16]
          }, {
            name: 'visa',
            pattern: /^4/,
            valid_length: [16]
          }, {
            name: 'mastercard',
            pattern: /^5[1-5]/,
            valid_length: [16]
          }, {
            name: 'maestro',
            pattern: /^(5018|5020|5038|6304|6759|676[1-3])/,
            valid_length: [12, 13, 14, 15, 16, 17, 18, 19]
          }, {
            name: 'discover',
            pattern: /^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,
            valid_length: [16]
          }
        ];


        var _j, _len1, _ref1, card, card_type, options = {};
        var __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };


        if (options.accept == null) {
            options.accept = (function() {
            var _i, _len, _results;
                _results = [];

            for (_i = 0, _len = card_types.length; _i < _len; _i++) {
                card = card_types[_i];
                _results.push(card.name);
            }

            return _results;

            })();
        }

        var  _ref = options.accept;
        for (var _i = 0, _len = _ref.length; _i < _len; _i++) {
            card_type = _ref[_i];
            
            if (__indexOf.call((function() {
                var _j, _len1, _results = [];

                for (var _j = 0, _len1 = card_types.length; _j < _len1; _j++) {
                    card = card_types[_j];
                    _results.push(card.name);
                }

                return _results;

            })(), card_type) < 0) {

                // throw "Credit card type '" + card_type + "' is not supported";

            }
        }

        _ref1 = (function() {
            var _k, _len1, _ref1, _results = [];
            
            for (_k = 0, _len1 = card_types.length; _k < _len1; _k++) {
                card = card_types[_k];
                if (_ref1 = card.name, __indexOf.call(options.accept, _ref1) >= 0) {
                    _results.push(card);
                }
            }

            return _results;

        })();

        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
            card_type = _ref1[_j];

            if (number.match(card_type.pattern)) {
                return card_type;
            }

        }

        return null;


        /**
        var re = {
            electron:       /^(4026|417500|4405|4508|4844|4913|4917)\d+$/,
            maestro:        /^(5018|5020|5038|5612|5893|6304|6759|6761|6762|6763|0604|6390)\d+$/,
            dankort:        /^(5019)\d+$/,
            interpayment:   /^(636)\d+$/,
            unionpay:       /^(62|88)\d+$/,
            visa:           /^4[0-9]{12}(?:[0-9]{3})?$/,
            mastercard:     /^5[1-5][0-9]{14}$/,
            amex:           /^3[47][0-9]{13}$/,
            diners:         /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
            discover:       /^6(?:011|5[0-9]{2})[0-9]{12}$/,
            jcb:            /^(?:2131|1800|35\d{3})\d{11}$/
        };

        for(var key in re) {
            if(re[key].test(number)) {
                return key;
            }
        }
        **/

    }

  }

})(jQuery);
/**
 *
 *  [SOW] Check All
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.checkall.init('input[data-checkall-container]');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Check All';
  var checkList = [];

  $.SOW.core.checkall = {


    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')


      if(jQuery(this.selector).length < 1)
        return;

      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      $.SOW.core.checkall.process(this.selector);
      return null;

    },



    /**
     *
     *  @process
     *  
     *
     **/
    process: function(selector) {


      document.querySelectorAll( selector ).forEach( function( el ) {

        // ignore multiple bind -- -- -- -- -- -- -- -- --
        if( el.classList.contains('js-init-checkall') ) return;
          el.classList.add('js-init-checkall');
        // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

        let target = el.getAttribute('data-checkall-container');
        if( !target || !document.querySelector( target ) ) return;

        if( checkList.indexOf(target) === -1 )
          checkList.push( target );

      });


      for( let i in checkList ) {

        /** 
          Variables
        **/
        let checkallList = document.querySelector( checkList[i] );
        if( !checkallList ) continue;
        let sAll  = document.querySelectorAll('[data-checkall-container="'+checkList[i]+'"]');
        let items = checkallList.querySelectorAll('[type="checkbox"]:not(.js-ignore):not(:disabled)');


        /** 
          Item click
        **/
        items.forEach(function( item ) {
          item.addEventListener('click', function(e) {
            checkallStates();
          });
        });


        /** 
          Checkall click 
        **/
        sAll.forEach( function( sItem ) {

          sItem.addEventListener('click', function(c) {

            /* select all */
            if( c.target.checked ) { 
              items.forEach(function( item ) { 
                if( !item.disabled ) item.checked = true;
              });
            } else { /* deselect all */
              items.forEach(function( item ) { 
                if( !item.disabled ) item.checked = false;
              });
            }

            checkallStates();

          });

        });



        /** 
          Checkall states 
        **/
        function checkallStates() {

          let checkedItems = checkallList.querySelectorAll('[type="checkbox"]:checked:not(.js-ignore):not(:disabled)').length;
          sAll.forEach( function( t ) {

            if( checkedItems < 1 ) {
             
              /* none checked */
              t.indeterminate = false;
              t.checked = false;
              // console.log('checked : none', checkedItems+'/'+items.length);
            
            } else {

              /* partially checked */
              if( checkedItems < items.length ) {
                t.checked = false;
                t.indeterminate = true;
                // console.log('checked : partial', checkedItems+'/'+items.length);
              }

              /* all checked */
              else  { 
                t.checked = true;
                t.indeterminate = false;
                // console.log('checked : all', checkedItems+'/'+items.length);
              }

            }

          });

        }



        /**
          On Load
        **/ checkallStates();

      };


    },


  };


})(jQuery);
/**
 *
 *  [SOW] Check Group
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.checkgroup.init('div.checkgroup');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Check Group';


  $.SOW.core.checkgroup = {


    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     *  
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')


      if(jQuery(this.selector).length < 1)
          return;

      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      $.SOW.core.checkgroup.process(this.collection);
      return null;

    },



    /**
     *
     *  @process
     *  
     *
     **/
    process: function(_this) {

      _this.not('.js-checkgroup').addClass('js-checkgroup').each(function () {

        var _t              = jQuery(this),
            isUnique        = _t.attr('data-checkgroup-checkbox-unique') || _t.attr('data-checkgroup-checkbox-single') || 'false',
            ajaxURL         = _t.attr('data-checkgroup-ajax-url')               || '',
            ajaxMethod      = _t.attr('data-checkgroup-ajax-method')            || 'GET',
            ajaxParams      = _t.attr('data-checkgroup-ajax-params')            || '',
            toastMsg        = _t.attr('data-checkgroup-ajax-toast-success')     || '',
            toastPosition   = _t.attr('data-checkgroup-ajax-toast-position')    || 'top-center';


        // Custom Params
        var ajaxParamObj = { ajax:'true' };
        if(ajaxParams != '') {

          var ajax_params_arr = $.SOW.helper.params_parse(ajaxParams);
          for (var i = 0; i < ajax_params_arr.length; ++i) {
            ajaxParamObj[ajax_params_arr[i][0]] = ajax_params_arr[i][1];
          }

        }


        // each checkbox/radio click
        jQuery('input[type=checkbox], input[type=radio]', _t).each(function() {

          jQuery(this).on('click', function() {

            // Switch radios
            if(isUnique+'' == 'true')
              jQuery('input[type=checkbox]', _t).not(this).prop('checked', false);


            // Ajax Only!
            if(ajaxURL != '') {

              // get all elements from checkgroup
              var itemList = [], $i = 0;
              jQuery('input[type=checkbox], input[type=radio]', _t).each(function() {

                // Item optional params
                var itemParams      = jQuery(this).attr('data-params') || '';
                var itemParamsObj   = {};
                if(itemParams != '') {

                  var ajax_params_arr = $.SOW.helper.params_parse(itemParams);
                  for (var i = 0; i < ajax_params_arr.length; ++i) {
                    itemParamsObj[ajax_params_arr[i][0]] = ajax_params_arr[i][1];
                  }

                }
                // --

                // create object for each element
                itemList[$i++] = {
                  name:   jQuery(this).attr('name')   || '',
                  value:  jQuery(this).val()          || '',
                  active: jQuery(this).is(':checked') ? 1 : 0,
                  params: itemParamsObj
                };

              });

              // Ajax Values
              ajaxParamObj['items'] = itemList;

              // Debug
              if($.SOW.config.sow__debug_enable === false)
                console.log(ajaxMethod, ajaxParamObj);

              // Ajax Request
              $.SOW.core.checkgroup.ajaxRequest(ajaxURL, ajaxMethod, ajaxParamObj, toastMsg, toastPosition);
          
            }

          });

        });

      });

    },




    /**
     *
     *  @ajaxRequest
     *  
     *
     **/
    ajaxRequest: function(ajaxURL, ajaxMethod, ajaxParamObj, toastMsg, toastPosition) {

      // Ajax
      // JQUERY USED because is able to send data as multidimmensional 
      jQuery.ajax({
        url:            ajaxURL,
        type:           ajaxMethod || 'GET',
        data:           ajaxParamObj,
        debug:          false,
        success:    function(data) { 

          // Debug
          if($.SOW.config.sow__debug_enable === true)
            console.log(data);
                  
          if( toastMsg && typeof $.SOW.core.toast === 'object' ) {
            $.SOW.core.toast.destroy();
            $.SOW.core.toast.show('success', '', toastMsg, toastPosition, 1500, true);
          }

        }
      });

    }

  };


})(jQuery);


/**
 *
 *  [SOW] Inline Search
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     -
 *  @Usage          $.SOW.core.search_inline.init('input.iqs-input');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Inline Search';


  $.SOW.core.search_inline = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {},



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     * 
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      if(jQuery(this.selector).length < 1)
        return;


      /* Case Sensitive :contains */
      jQuery.extend(jQuery.expr[":"], {
        "containsIN": function(elem, i, match, array) {
          return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
        }
      });



        // -- * --
        $.SOW.helper.consoleLog('Init : ' + scriptInfo);
        // -- * --


        // 1. Has no selector
        if(!this.selector) {
          $.SOW.core.search_inline.process($('input.iqs-input'));
          return this.collection;
        }

        // 2. Has selector
        return this.collection.each(function() {
            
          $.SOW.core.search_inline.process($(this));

        });

    },



    /**
     *
     *  @process
     *

        <input type="text" class="form-control iqs-input" data-container=".iqs-container" name="quick-filter" value="" placeholder="quick filter">

        <div class="iqs-container">

          <div class="iqs-item">
            <span>Something Here</span>
          </div>

          <div class="iqs-item">
            <span>Another Here</span>
          </div>
          
          ...

        </div>

     *
     *
     **/
    process: function(_this) {

      // --
      if(_this.hasClass('iqs-init'))
        return;
      
      _this.addClass('iqs-init');
      // --


      var _container = _this.data('container') || '.iqs-container';

      _this.keyup(function() {

        var keywords = jQuery.trim(this.value);

        if (keywords == "") {

          jQuery(_container+" .iqs-item").removeClass('hide hide-force');
        
        } else {

          jQuery(_container+" .iqs-item").addClass('hide hide-force'); 
          jQuery(_container+' .iqs-item :containsIN('+keywords+')').closest(".iqs-item").removeClass('hide hide-force');

        }

      });

    },

  };


})(jQuery);
/**
 *
 *  [SOW] Input Suggest
 *
 *  @author         Dorin Grigoras
 *                  www.stepofweb.com
 *
 *  @Dependency     $.SOW.core.search_inline (for inline search)
 *  @Usage          $.SOW.core.input_suggest.init('input.input-suggest');
 * 
 *
 **/
;(function ($) {
  'use strict';


  /**
   *
   *  @vars
   *
   *
   **/
  var scriptInfo      = 'SOW Input Suggest';


  $.SOW.core.input_suggest = {


    /**
     *
     *  @config
     *
     *
     **/
    config: {},



    /**
     *
     *  @collection
     *
     *
     **/
    collection: $(),



    /**
     *
     *  @init
     * 
     *
     **/
    init: function (selector, config) {

      var __selector          = $.SOW.helper.__selector(selector);
      var __config            = $.SOW.helper.check_var(config);

      this.selector           = __selector[0];    // '#selector'
      this.collection         = __selector[1];    // $('#selector')
      this.config             = (__config !== null) ? $.extend({}, this.config, __config) : this.config;


      if(jQuery(this.selector).length < 1)
        return;


      // -- * --
      $.SOW.helper.consoleLog('Init : ' + scriptInfo);
      // -- * --


      // 1. Has no selector
      if(!this.selector) {
        $.SOW.core.input_suggest.process($('input.input-suggest'));
        return this.collection;
      }

      // 2. Has selector
      return this.collection.each(function() {
          
        $.SOW.core.input_suggest.process($(this));

      });

    },



    /**
     *
     *  @process
     *
     *
     *
     **/
    process: function(_this) {

      // --
      if(_this.hasClass('is-init'))
        return;
      
      _this.addClass('is-init');
      // --


      var _elID           = _this.attr('id')                                  || '',
          _key            = _this.data('input-suggest-name')                  || _this.attr('name'),
          _maxItems       = _this.data('input-suggest-max-items')             || 0,
          _maxItems       = Number(_maxItems),
          _url            = _this.data('input-suggest-ajax-url')              || '',
          _method         = _this.data('input-suggest-ajax-method')           || 'GET',
          _action         = _this.data('input-suggest-ajax-action')           || '',
          _limit          = _this.data('input-suggest-ajax-limit')            || '10',
          _type           = _this.data('input-suggest-mode')                  || 'text', // text|append
          _delay          = _this.data('input-suggest-typing-delay')          || '100',
          _charMin        = _this.data('input-suggest-typing-min-char')       || '3',
          _container      = _this.data('input-suggest-append-container')      || '',
          _stripTags      = _this.attr('data-input-suggest-append-stip-tags') || 'true';

      if(_url == '') return;

      // Assign a random id if not exist
      if(_elID == '') {
        var _elID = 'js_' + $.SOW.helper.randomStr(5);
        _this.attr('id', _elID);
      }


      // find container - used on clonned
      if(_container == 'parent:group') {
        _this.closest('.input-suggest-group').find('.input-suggest-container').addClass('res_'+_elID);
        var _container = '.res_'+_elID;
      }


      // on search
      if(_type == 'append' || _type == 'self') {

        _this.on('keyup change', function(e) {

          if(window.afterSearchKeyUp)
            clearTimeout(window.afterSearchKeyUp);

          window.afterSearchKeyUp = setTimeout(function() {

            var _key = _this.val(),
                _key = _key.trim();

            if(_action == '')
              _action = 'input_search';

            if(_key.length >= Number(_charMin)) {
              jQuery('#dd_'+_elID+'>div').empty().append('<div class="text-center"><i class="fi fi-circle-spin fi-spin text-muted fs--30"></i></div>');
              $.SOW.core.input_suggest.__suggestAjax(_url,_method,_limit,_action,_key,_elID,_this,_type,_container,_maxItems,_stripTags);
            } else {
              jQuery('#dd_'+_elID+'>div').empty();
            }

          }, Number(_delay));

        });


        // On load : Remove appended
        if(_type == 'append')
          $.SOW.core.input_suggest.__suggestAppendedRemove(_container);


      } else {

        // on input click
        _this.on('click', function(e) {

          var _this = jQuery(this);
          if(_action == '')
            _action = 'input_suggest';

          // Process Ajax
          if(jQuery('#dd_'+_elID).length < 1) {
            $.SOW.core.input_suggest.__suggestAjax(_url,_method,_limit,_action,_key,_elID,_this,_type,_container,_maxItems,_stripTags);
            return;
          }

          if(_this.val() == '')
            jQuery('#dd_'+_elID+' .iqs-item').removeClass('hide hide-force');


        });

      }

    },



    /**
     *
     *  @__suggestAjax
     *
     *
     *
     **/
    __suggestAjax: function(_url,_method,_limit,_action,_key,_elID,_this,_type,_container,_maxItems,_stripTags) {

        var __data = { ajax:"true", action:_action, key:_key, limit:_limit },
            __name = _this.data('name') || _this.attr('name');

        jQuery.ajax({

          url:            _url,
          data:           __data,
          type:           _method,
          cache:          false,
          contentType:    'application/x-www-form-urlencoded; charset=UTF-8',
          dataType:       null,
          headers:        '',
          crossDomain:    '',

          error:  function(XMLHttpRequest, textStatus, errorThrown) {

            $.SOW.helper.consoleLog('Input search suggest Error!');
            $.SOW.helper.consoleLog('URL: '+ _url);
            $.SOW.helper.consoleLog(__data);

          },

          success: function(data) {

              $.SOW.helper.consoleLog('Input search suggest Request!');
              $.SOW.helper.consoleLog('URL: '+ _url);
              $.SOW.helper.consoleLog(__data);

              // Parse JSON
              var _data = $.SOW.core.input_suggest.__suggestJsonParse(data);
              if(_data === null)
                  return;

              // Dropdown TPL
              $.SOW.core.input_suggest.__suggestTPL(_this,_elID);

              // reset
              jQuery('#dd_'+_elID+'>div').empty();
              if(_data.length < 1)  
                  return;

              // Append Data
              for (var i = 0; i < _data.length; i++) {

                  if(_type == 'append' || _type == 'self') {
                      
                      if(_type == 'self') {
                          _data[i].url = '#!';
                          _data[i].id  = '';
                      }

                      if(_data[i].disabled == true)
                          jQuery('#dd_'+_elID+'>div').append('<div class="dropdown-item p-2 text-muted">'+_data[i].label+'</div>');
                      else
                          jQuery('#dd_'+_elID+'>div').append('<a href="'+_data[i].url+'" data-id="'+_data[i].id+'" class="dropdown-item px-2"><span>'+_data[i].label+'</span></a>');
                  }
                  else
                      jQuery('#dd_'+_elID+'>div').append('<a href="#!" class="iqs-item dropdown-item px-2"><span>'+_data[i]+'</span></a>');

              }

              // Activate Bootstrap Dropdown
              if(_this.attr('data-bs-toggle') != 'dropdown') {
                  _this.attr('data-bs-toggle', 'dropdown');
                  _this.dropdown('show');
              }

              // SOW inline search 
              if(_type == 'text' && typeof $.SOW.core.search_inline === 'object') {
                  _this.attr('data-container', '#dd_'+_elID+'>div');
                  _this.addClass('iqs-input');
                  $.SOW.core.search_inline.init('input.iqs-input');
              }

              // Activate click
              jQuery('#dd_'+_elID+' a.dropdown-item').off().on('click', function(e) {
                  e.preventDefault();

                  var _it             = jQuery(this),
                      item_label      = (_stripTags+'' == 'true') ? _it.text() : _it.html(),
                      item_url        = _it.attr('href')      || '#',
                      item_id         = _it.data('id')        || '0',
                      item_disabled   = _it.data('disabled')  || false;

                  if(_type == 'append') {

                      $.SOW.core.input_suggest.__suggestAppend(item_label, item_url, item_id, item_disabled, __name, _container);

                      if(_maxItems > 0) {
                          var _currItems = jQuery('>div', _container).length;
                          if(Number(_currItems) >= _maxItems)
                              _this.prop('disabled', true);
                      }

                  }
                  else
                      _this.val(item_label);

              });

          }

        });

    },




    /**
     *
     *  @__suggestJsonParse
     *
     *
     *
     **/
    __suggestJsonParse: function(data) {

      // check
      if(data == '')
        return data;

      // parse json
      try {

        var _data = JSON.parse(data);

      } catch(err) {

        var _data = data;

      }

      if(typeof _data === 'undefined' || _data.length < 1)
        return null;

      // check
      if(typeof _data[0].label === 'undefined' && typeof _data[0].length === 'undefined')
        return null;

      // return parsed
      return _data;

    },





    /**
     *
     *  @__suggestTPL
     *
     *
     *
     **/
    __suggestTPL: function(_this,_elID) {

      var __el = _this;

      // create dropdown container
      if(jQuery('#dd_'+_elID).length < 1) {

        // If input is inside a <label>
        if(_this.parents('label').length > 0) {
          _this.parents('label').addClass('dropdown');
          __el = _this.parents('label');
        }

        // If input is directly inside a <form>
        else if(_this.parent('form').length > 0) {
          _this.parent('form').addClass('dropdown');
          __el = _this;
        }

        else if(_this.parents('.input-group').length > 0) {
          _this.parents('.input-group').addClass('dropdown');
          __el = _this;
        }
        
        else if(_this.parents('.form-label-group').length > 0) {
          _this.parents('.form-label-group').addClass('dropdown');
          __el = _this.next('label');
        
        } else {
          _this.wrap('<div class="dropdown"></div>');
        }

        // Create Dropdown
        __el.after('<div id="dd_'+_elID+'" class="dropdown-menu p-1 w-100"><div style="max-height:200px" class="scrollable-vertical"></div></div>');

      }

      return __el;

    },


    /**
     *
     *  @__suggestAppend
     *
     *
     **/
    __suggestAppend: function(item_label, item_url, item_id, item_disabled, __name, _container) {

      var __name = (__name == '') ? 'item[]' : __name;

      var _tpl = '<div class="p-1 clearfix">'
                      + '<a href="#!" class="item-suggest-append-remove fi fi-close float-start text-decoration-none" style="font-size:16px"></a>';
                      
                      if(item_url != '' && item_url != '#' && item_url != '#!')
                          _tpl += '<a href="'+item_url+'" target="_blank" class="text-decoration-none">'+item_label+'</a>';
                      else 
                          _tpl += '<span>'+item_label+'</span>';

                      _tpl += '<input type="hidden" name="'+__name+'" value="'+item_id+'">'
               + '</div>';

      // add item
      jQuery(_container).append(_tpl);

      // Remove button
      $.SOW.core.input_suggest.__suggestAppendedRemove(_container);

    },



    /**
     *
     *  @__suggestAppendedRemove
     *
     *
     **/
  __suggestAppendedRemove: function(_container) {

      if(_container == '')
        return;

      // Remove button
      jQuery(_container + ' a.item-suggest-append-remove').off().on('click', function(e) {
        e.preventDefault();

        jQuery(this).parent().remove();

        // re-enable input (if disabled)
        var _suggestEl = jQuery(_container).parent().find('input.is-init');
        if(_suggestEl.length > 0) _suggestEl.prop('disabled', false);
        else jQuery('input.is-init').prop('disabled', false);

      });

    }


  };

})(jQuery);


/**
 * fuzzy.js v0.1.0
 * (c) 2016 Ben Ripkens
 * @license: MIT
 */
(function (context) {
  'use strict';

  var fuzzy = function fuzzy(term, query) {
    var max = calcFuzzyScore(term, query);
    var termLength = term.length;

    if (fuzzy.analyzeSubTerms) {

      for (var i = 1; i < termLength && i < fuzzy.analyzeSubTermDepth; i++) {
        var subTerm = term.substring(i);
        var score = calcFuzzyScore(subTerm, query);
        if (score.score > max.score) {
          // we need to correct 'term' and 'matchedTerm', as calcFuzzyScore
          // does not now that it operates on a substring. Doing it only for
          // new maximum score to save some performance.
          score.term = term;
          score.highlightedTerm = term.substring(0, i) + score.highlightedTerm;
          max = score;
        }
      }
    }

    return max;
  };

  var calcFuzzyScore = function calcFuzzyScore(term, query) {
    var score = 0;
    var termLength = term.length;
    var queryLength = query.length;
    var highlighting = '';
    var ti = 0;
    // -1 would not work as this would break the calculations of bonus
    // points for subsequent character matches. Something like
    // Number.MIN_VALUE would be more appropriate, but unfortunately
    // Number.MIN_VALUE + 1 equals 1...
    var previousMatchingCharacter = -2;

    for (var qi = 0; qi < queryLength && ti < termLength; qi++) {
      var qc = query.charAt(qi);
      var lowerQc = qc.toLowerCase();

      for (; ti < termLength; ti++) {
        var tc = term.charAt(ti);

        if (lowerQc === tc.toLowerCase()) {
          score++;

          if ((previousMatchingCharacter + 1) === ti) {
            score += 2;
          }

          highlighting += fuzzy.highlighting.before +
              tc +
              fuzzy.highlighting.after;
          previousMatchingCharacter = ti;
          ti++;
          break;
        } else {
          highlighting += tc;
        }
      }
    }

    highlighting += term.substring(ti, term.length);

    return {
      score: score,
      term: term,
      query: query,
      highlightedTerm: highlighting
    };
  };

  fuzzy.matchComparator = function matchComparator(m1, m2) {
    return (m2.score - m1.score != 0) ? m2.score - m1.score : m1.term.length - m2.term.length;
  };

  /*
   * Whether or not fuzzy.js should analyze sub-terms, i.e. also
   * check term starting positions != 0.
   *
   * Example:
   * Given the term 'Halleluja' and query 'luja'
   *
   * Fuzzy.js scores this combination with an 8, when analyzeSubTerms is
   * set to false, as the following matching string will be calculated:
   * Ha[l]lel[uja]
   *
   * If you activate sub temr analysis though, the query will reach a score
   * of 10, as the matching string looks as following:
   * Halle[luja]
   *
   * Naturally, the second version is more expensive than the first one.
   * You should therefore configure how many sub terms you which to analyse.
   * This can be configured through fuzzy.analyzeSubTermDepth = 10.
   */
  fuzzy.analyzeSubTerms = false;

  /*
   * How many sub terms should be analyzed.
   */
  fuzzy.analyzeSubTermDepth = 10;

  fuzzy.highlighting = {
    before: '<em>',
    after: '</em>'
  };

  /*
   * Exporting the public API
   * ------------------------
   * In a browser, the library will be available through this.fuzzy. Should
   * requireJS be used, we register fuzzy.js through requireJS.
   * For other environments, CommonJS is supported.
   */
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = fuzzy;
  } else if (typeof define === 'function') {
    define(function() {
      return fuzzy;
    });
  } else {
    /*
     * In case the global variable fuzzy needs to be reset to its previous
     * value, the fuzzy library is returned by this method.
     */
    var previousFuzzy = context.fuzzy;
    fuzzy.noConflict = function() {
      context.fuzzy = previousFuzzy;
      return fuzzy;
    };

    context.fuzzy = fuzzy;
  }
})(this);

if(typeof $.SOW.config.autoinit === 'undefined') { $.SOW.config.autoinit = {}; }
$.SOW.config.autoinit['sow_ajax_navigation'] = ['$.SOW.core.ajax_navigation.init','a.js-ajax', {enable:true,target_container:"#middle",parser_extract_enable:true,parser_force_push2target:false,parser_extract_element:"#middle, #page_js_files",page_404:"404.html",container2_element:"img.js-ajax-loaded-animate",container2_class:"animate-bouncein",data_params:{ajax:"true"},update_url:true,show_loading_icon:true,method:"GET",autoscroll_to_content:true,callback_function:"",callback_before_push:false,onpopstate_reload:true,custom_function_call:"",contentType:"",dataType:"",headers:"",crossDomain:"",ajaxGarbage:""},true];
$.SOW.config.autoinit['sow_ajax_content'] = ['$.SOW.core.ajax_content.init','div.js-ajax, section.js-ajax', null,true];
$.SOW.config.autoinit['sow_ajax_form'] = ['$.SOW.core.ajax_form.init','form.js-ajax', null,true];
$.SOW.config.autoinit['sow_ajax_modal'] = ['$.SOW.core.ajax_modal.init','.js-ajax-modal', {size:"modal-md"},true];
$.SOW.config.autoinit['sow_ajax_confirm'] = ['$.SOW.core.ajax_confirm.init','.js-ajax-confirm', null,true];
$.SOW.config.autoinit['sow_checkall'] = ['$.SOW.core.checkall.init','input[data-checkall-container]', null,true];
$.SOW.config.autoinit['sow_checkgroup'] = ['$.SOW.core.checkgroup.init','div.checkgroup', null,true];
