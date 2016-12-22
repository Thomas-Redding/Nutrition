function Shared_GUI() {
	var self = this;
	self.resize_calls = new Array();
	self.onload_calls = new Array();
	self.load_stuff_calls = new Array();
	self.loaded_and_confirmed = 0;

	window.onresize = function(event) {
		for (var i = 0; i < self.resize_calls.length; ++i)
			self.resize_calls[i](event);
	}

	self.add_resize_callback = function(callback) {
		self.resize_calls.push(callback);
	}

	self.get_em_in_pixels = function(div) {
		return parseFloat(getComputedStyle(div)["font-size"]);
	}

	self.add_onload_callback = function(callback) {
		self.onload_calls.push(callback);
	}

	window.onload = function(event) {
		++self.loaded_and_confirmed;
		for (var i = 0; i < self.onload_calls.length; ++i)
			self.onload_calls[i](event);
		if (self.loaded_and_confirmed == 2) {
			self.load_stuff();
		}
	}

	self.add_load_stuff_callback = function(callback) {
		self.load_stuff_calls.push(callback);
	}

	self.confirm_token = function() {
		++self.loaded_and_confirmed;
		if (self.loaded_and_confirmed == 2) {
			self.load_stuff();
		}
	}

	self.load_stuff = function() {
		for (var i = 0; i < self.load_stuff_calls.length; ++i)
			self.load_stuff_calls[i]();
	}
}

var shared_gui = new Shared_GUI();