
function Cache() {
	var self = this;

	self.isCachingAvailable = function() {
		// https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API
		try {
			var storage = window['localStorage'], x = '__storage_test__';
			storage.setItem(x, x);
			storage.removeItem(x);
			return true;
		}
		catch (e) {
			return false;
		}
	}
	
	/*
	day = {
		nutrition: {...}
		foods: [ {foodId: x, servingType: y, servingSize: z, numOfServings: w} , ...]
	}
	*/
	self.getDay = function(date) {
		return self._get("date" + date.yyyymmdd());
	}
	
	self.setDay = function(date, data) {
		self._set("date" + date.yyyymmdd(), data);
	}
	
	self.addFoodName = function(foodId, foodName) {
		var x = self._get("food-name-data");
		if (x === undefined || x === null)
			x = {};
		x[foodId] = foodName;
		self._set("food-name-data", x);
	}
	
	self.getFoodNames = function() {
		return self._get("food-name-data");
	}
	
	self.addFoodData = function(foodId, foodData) {
		self._set(foodId, foodData);
	}
	
	self.getFoodData = function(foodId) {
		return self._get(foodId);
	}

	self.setSecurityToken = function(token) {
		self._set("food-security-token", token);
	}

	self.setUsername = function(token) {
		self._set("food-username", token);
	}

	self.getSecurityToken = function() {
		return self._get("food-security-token");
	}

	self.getUsername = function() {
		return self._get("food-username");
	}

	self._set = function(key, value) {
		localStorage.setItem(key, window.JSON.stringify(value));
	}

	self._get = function (key) {
		return window.JSON.parse(localStorage.getItem(key));
	}

	self._delete = function(key) {
		localStorage.removeItem(key);
	}

	self._eraseAll = function() {
		localStorage.clear();
	}
}

// http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
Date.prototype.yyyymmdd = function() {
	var mm = this.getMonth() + 1; // getMonth() is zero-based
	var dd = this.getDate();
	return [this.getFullYear(), !mm[1] && '0', mm, !dd[1] && '0', dd].join(''); // padding
};

var cache = new Cache();
