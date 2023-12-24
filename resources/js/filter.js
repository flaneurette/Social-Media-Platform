const cssRules = [];

class filters {

	createFilters(input, imageid, filter, presets=false, result) {

		this.aim = input;
		this.filter = filter;
		this.result = result;
		this.imageid = imageid;

		const controls = document.createElement("div");

		controls.id = "controls";
		controls.style.display = "block";

		if(presets != false) {
			this.createPresets(this.imageid, controls, presets, this.result);
		}
		
		this.createSliders(this.aim, this.imageid, controls, this.filter, this.result);

		document.getElementById(this.aim).appendChild(controls);
	}

	createPresets(aim, controls, presets,result) {
		
		if (Array.isArray(presets)) {
			
			var selection = document.createElement("select");
				selection.addEventListener("change",
				function(event) {
						var preset = new filters;
						preset.newPreset(aim,event.currentTarget.value,result);
				});
					
			controls.appendChild(selection);
				
			for (var i = 0; i < presets.length; i++) {
				
				var control = document.createElement("option");
				control.name = presets[i][0];
				control.id = presets[i][0];
				control.value = presets[i][1];
				control.innerHTML = presets[i][0];
				selection.appendChild(control);
			}
		}
	}

	createSliders(aim, imageid, controls, filter, result) {

		if (Array.isArray(filter)) {

			for (var i = 0; i < filter.length; i++) {

				var label = document.createElement("label");
				var currentFilter = filter[i].split(':');
				label.innerHTML = currentFilter[0];
				controls.appendChild(label);

				var control = document.createElement("input");
				control.type = 'range';
				control.name = currentFilter[0];
				control.id = currentFilter[0];
				control.value = currentFilter[3];
				control.min = currentFilter[1];
				control.max = currentFilter[2];

				(function(currentFilter) {
					control.addEventListener("change",
						function(event) {
							var filterlist = new filters;
							filterlist.addFilter(aim, imageid, currentFilter[0], event.currentTarget.value, result);
						});
				})(currentFilter);

				controls.appendChild(control);
			}
		}
	}
	
	newPreset(aim,values,result) {
		
		var computed = 'filter:';
		var denom = '';
		var values = values.split(',');
		
		if(aim) { 
		
			for(var i=0; i< values.length;i++ ) {
				
				var pre = values[i].split(':');
				
					switch (pre[0]) {

					case 'Blur':
						denom = 'px';
					break;
					case 'Brightness':
					case 'Contrast':
					case 'Grayscale':
					case 'Invert':
					case 'Opacity':
					case 'Sepia':
						denom = '%';
					break;
					
					case 'Saturate':
						denom = '';
					break;
					case 'Hue':
						denom = 'deg';
						pre[0] = 'hue-rotate';
					break;
				}
				
				try {
					document.getElementById(pre[0]).value = pre[1];
				} catch(e) {}
				
				computed += pre[0].toLowerCase() + '(' + pre[1] + denom + ')';
			}
		
			document.getElementById(aim).style = computed + ';';
			document.getElementById(result).value = computed + ';';
		}
	}
	
	resetFilters(imageid) {
		
		let filterlist = [
			'Brightness:0:200:100',
			'Contrast:0:200:100',
			'Grayscale:0:100:0',
			'Hue:0:360:0',
			'Invert:0:100:0',
			'Opacity:0:100:100',
			'Saturate:0:10:0',
			'Sepia:0:100:0',
			'Blur:0:10:0'
		];
		
		for(var i=0; i< filterlist.length;i++ ) {
			var pre = filterlist[i].split(':');
				try {
					document.getElementById(pre[0]).value = pre[3];
				} catch(e) {}
		}
		
		document.getElementById(imageid).style = '';
	}


	result(id, value) {
		document.getElementById(id).value = value;
	}

	pushCSS(value, imageid, result) {

		for (var k = 0; k < cssRules.length; k++) {
			var test = value.split('(');
			if (cssRules[k].match(new RegExp(test[0]))) {
				cssRules[k] = '';
			}
		}

		cssRules.push(value);

		var styled = window.getComputedStyle(document.getElementById(imageid), null).filter;
		var object = document.getElementById(imageid);
		var pushedCSS = '';

		for (var j = 0; j < cssRules.length; j++) {
			pushedCSS += cssRules[j];
		}

		object.style.setProperty("filter", pushedCSS);
		document.getElementById(result).value = 'filter:' + pushedCSS + ';';
	}

	addFilter(id, imageid, filter, value, result) {

		if (id != null) {

			var object = document.getElementById(imageid);
			var style = document.getElementById(result).style;

			if (value != null) {

				switch (filter) {

					case 'Blur':
						this.pushCSS("blur(" + value + "px)", imageid, result);
						break;
					case 'Brightness':
						this.pushCSS("brightness(" + value + "%)", imageid, result);
						break;
					case 'Contrast':
						this.pushCSS("contrast(" + value + "%)", imageid, result);
						break;
					case 'Grayscale':
						this.pushCSS("grayscale(" + value + "%)", imageid, result);
						break;
					case 'Hue':
						this.pushCSS("hue-rotate(" + value + "deg)", imageid, result);
						break;
					case 'Invert':
						this.pushCSS("invert(" + value + "%)", imageid, result);
						break;
					case 'Opacity':
						this.pushCSS("opacity(" + value + "%)", imageid, result);
						break;
					case 'Saturate':
						this.pushCSS("saturate(" + value + ")", imageid, result);
						break;
					case 'Sepia':
						this.pushCSS("sepia(" + value + "%)", imageid, result);
						break;
					default:
						object.style = "filter:none;";
				}
			}
		}
	}
}