/************************************
*
* last update 29.10.2015
*
************************************/
;(function(window) {

if (!!window.RS.Color)
	return;

window.RS.Color = function(hex) {
  if (!!hex) {
    this._hex = this._checkHex(hex);
    this._hsl = this.hex2Hsl(this._hex);
    this._rgb = this.hex2Rgb(this._hex);
  }
  else {
    return new RS.Color('000000');
  }
}
  
RS.Color.prototype.DEFAULT_ADJUST = 10;
RS.Color.prototype.hex2Hsl = function(hex) {
  hex = this._checkHex(hex);
  if (!hex) {
    return false;
  }
  var R = parseInt('0x' + hex.substr(0, 2)),
      G = parseInt('0x' + hex.substr(2, 2)),
      B = parseInt('0x' + hex.substr(4, 2)),
      var_R = (R / 255),
      var_G = (G / 255),
      var_B = (B / 255),
      var_Min = Math.min(var_R, var_G, var_B),
      var_Max = Math.max(var_R, var_G, var_B),
      del_Max = var_Max - var_Min,
      HSL = {
        "H": 0,
        "S": 0,
        "L": (var_Max + var_Min) / 2,
      };
  if (del_Max == 0) {
    HSL['H'] = 0;
    HSL['S'] = 0;
  } else {
    if (HSL['L'] < 0.5) {
      HSL['S'] = del_Max / (var_Max + var_Min);
    } else {
      HSL['S'] = del_Max / (2 - var_Max - var_Min);	
    }
    del_R = (((var_Max - var_R) / 6) + (del_Max / 2)) / del_Max;
    del_G = (((var_Max - var_G) / 6) + (del_Max / 2)) / del_Max;
    del_B = (((var_Max - var_B) / 6) + (del_Max / 2)) / del_Max;
    if (var_R == var_Max) {
      HSL['H'] = del_B - del_G;
    } else if (var_G == var_Max) {
      HSL['H'] = (1 / 3) + del_R - del_B;
    } else if (var_B == var_Max) {
      HSL['H'] = (2 / 3) + del_G - del_R;
    }
    if (HSL['H'] < 0) {
      HSL['H']++;
    }
    if (HSL['H'] > 1) {
      HSL['H']--;
    }
  }
  HSL['H'] = (HSL['H'] * 360);
  return HSL;
};

RS.Color.prototype.hsl2Hex = function(hsl) {
  if (undefined == hsl || undefined == hsl['H'] || undefined == hsl['S'] || undefined == hsl['L']) {
    return false;
  }
  var r, g, b, H = hsl['H'] / 360, S = hsl['S'], L = hsl['L'];
  
  if (S == 0) {
    r = L * 255;
    g = L * 255;
    b = L * 255;
  } else {
    if (L < 0.5) {
      var_2 = L * (1 + S);
    } else {
      var_2 = (L + S) - (S * L);
    }
    var_1 = 2 * L - var_2;
    r = Math.round(255 * this._hue2Rgb(var_1, var_2, H + (1/3)));
    g = Math.round(255 * this._hue2Rgb(var_1, var_2, H));
    b = Math.round(255 * this._hue2Rgb(var_1, var_2, H - (1/3)));
  }

  r = r.toString(16).split('.')[0];
  g = g.toString(16).split('.')[0];
  b = b.toString(16).split('.')[0]; 

  r = (r.length === 1) ? '0' + r : r;
  g = (g.length === 1) ? '0' + g : g;
  b = (b.length === 1) ? '0' + b : b;

  return r + g + b;
}

RS.Color.prototype.hex2Rgb = function(hex) {
  if (!hex) {
    return false;
  }
  var hex = this._checkHex(hex),
    rgb = hex
      ? {
        "R": parseInt('0x' + hex.substr(0, 2)),
        "G": parseInt('0x' + hex.substr(2, 2)),
        "B": parseInt('0x' + hex.substr(4, 2)),
      }
      : false;
    return rgb;
}

RS.Color.prototype.rgb2Hex = function(rgb) {
  if (undefined == rgb || undefined == rgb['R'] || undefined == rgb['G'] || undefined == rgb['B']) {
    return false;
  }

  r = rgb['R'].toString(16);
  g = rgb['G'].toString(16);
  b = rgb['B'].toString(16);
  
  r = (r.length === 1) ? '0' + r : r;
  g = (g.length === 1) ? '0' + g : g;
  b = (b.length === 1) ? '0' + b : b;
  return r + g + b;
}

RS.Color.prototype.darken = function(amount) {
  var color = this.clone(this);
  if (amount) {
    color._hsl['L'] = (color._hsl['L'] * 100) - amount;
    color._hsl['L'] = (color._hsl['L'] < 0) ? 0 : color._hsl['L'] / 100;
  } else {
    color._hsl['L'] = color._hsl['L'] / 2 ;
  }
  color._hex = this.hsl2Hex(color._hsl);
  color._rgb = this.hex2Rgb(color._hex);
  return color;
}

RS.Color.prototype.lighten = function(amount) {
  var color = this.clone(this);
  if (amount) {
    color._hsl['L'] = (color._hsl['L'] * 100) + amount;
    color._hsl['L'] = (color._hsl['L'] > 100) ? 1 : color._hsl['L'] / 100;
  } else {
    color._hsl['L'] += (1 - color._hsl['L']) / 2;
  }
  color._hex = this.hsl2Hex(color._hsl);
  color._rgb = this.hex2Rgb(color._hex);
  return color;
}

RS.Color.prototype.adjustHue = function(deg) {
  var color = this.clone(this);
  color._hsl['H'] = (color._hsl['H'] + deg);
  color._hex = this.hsl2Hex(color._hsl);
  color._rgb = this.hex2Rgb(color._hex);
  return color;
}

RS.Color.prototype.saturate = function(amount) {
  var color = this.clone(this);
  if (amount) {
    color._hsl['S'] = (color._hsl['S'] * 100) + amount;
    color._hsl['S'] = (color._hsl['S'] > 100) ? 1 : color._hsl['S'] / 100;
  } else {
    color._hsl['S'] += (1 - color._hsl['S']) / 2;
  }
  color._hex = this.hsl2Hex(color._hsl);
  color._rgb = this.hex2Rgb(color._hex);
  return color;
}

RS.Color.prototype.desaturate = function(amount) {
  var color = this.clone(this);
  if (amount) {
    color._hsl['S'] = (color._hsl['S'] * 100) - amount;
    color._hsl['S'] = (color._hsl['S'] < 0) ? 0 : color._hsl['S'] / 100;
  } else {
    color._hsl['S'] = color._hsl['S'] / 2 ;
  }
  color._hex = this.hsl2Hex(color._hsl);
  color._rgb = this.hex2Rgb(color._hex);
  return color;
}

RS.Color.prototype.invert = function() {
  var color = this.clone(this);
  color._rgb['R'] = 255 - this._rgb['R'];
  color._rgb['G'] = 255 - this._rgb['G'];
  color._rgb['B'] = 255 - this._rgb['B'];
  
  color._hex = this.rgb2Hex(color._rgb);
  color._hsl = this.hex2Hsl(color._hex);
  return color;
}

RS.Color.prototype.getHsl = function() {
  return this._hsl;
}

RS.Color.prototype.getHex = function() {
  return this._hex;
}

RS.Color.prototype.getRgb = function(color) {
  if (color == 'R' || color == 'G' || color == 'B') {
    return this._rgb[color];
  } else {
    return this._rgb;
  }
}

RS.Color.prototype.getRgba = function(opacity) {
  return this._rgb['R'] + ', ' + this._rgb['G'] + ', ' + this._rgb['B'] + ', ' +  opacity;
}

RS.Color.prototype._hue2Rgb = function(v1, v2, vH) {
  if (vH < 0) {
    vH += 1;
  }
  if (vH > 1) {
    vH -= 1;
  }
  if ((6 * vH) < 1) {
    return (v1 + (v2 - v1) * 6 * vH);
  }
  if ( (2 * vH) < 1 ) {
    return v2;
  }
  if ((3 * vH) < 2) {
    return (v1 + (v2 - v1) * ((2 / 3) - vH) * 6);
  }
  return v1;
}

RS.Color.prototype._checkHex = function(hex) {
  if (!hex) {
    return false;
  }

  color = hex.replace('#', '');
  if (color.length == 3) {
    var r = color.substr(0, 1),
        g = color.substr(1, 1),
        b = color.substr(2, 1);
    color = r + r + g + g + b + b;
  } else if (color.length != 6) {
    return false;
  }
  return color;
}

RS.Color.prototype.clone = function(target) {
  if (target === null || typeof target !== 'object') {
    return target;
  }
  var clone = target.constructor();
  for (var key in target) {
    if (target.hasOwnProperty(key)) {
      clone[key] = this.clone(target[key]);
    }
  }
  return clone;
}

})(window);