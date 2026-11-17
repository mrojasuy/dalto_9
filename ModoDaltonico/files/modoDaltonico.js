(function() {
	'use strict';

	var config = window.ModoDaltonicoConfig || {};
	var initialActive = !!config.initialActive;
	var contrastType = config.contrastType || 'protanopia';
	var activeLabel = config.activeLabel || 'Active';
	var inactiveLabel = config.inactiveLabel || 'Inactive';
	var strings = config.strings || {};

	var STORAGE_KEY_ACTIVE = 'md_active';
	var STORAGE_KEY_TYPE = 'md_contrast_type';

	function getStorage(key, fallback) {
		try {
			var val = localStorage.getItem(key);
			return val !== null ? val : fallback;
		} catch (e) {
			return fallback;
		}
	}

	function setStorage(key, value) {
		try {
			localStorage.setItem(key, value);
		} catch (e) {
			// localStorage not available
		}
	}

	function injectSvgFilters() {
		var container = document.createElement('div');
		container.className = 'md-svg-filters';
		container.setAttribute('aria-hidden', 'true');
		container.innerHTML =
			'<svg xmlns="http://www.w3.org/2000/svg">' +
				'<defs>' +
					'<filter id="md-filter-protanopia">' +
						'<feColorMatrix type="matrix" values="' +
							'0.567, 0.433, 0,     0, 0 ' +
							'0.558, 0.442, 0,     0, 0 ' +
							'0,     0.242, 0.758, 0, 0 ' +
							'0,     0,     0,     1, 0" />' +
					'</filter>' +
					'<filter id="md-filter-deuteranopia">' +
						'<feColorMatrix type="matrix" values="' +
							'0.625, 0.375, 0,   0, 0 ' +
							'0.7,   0.3,   0,   0, 0 ' +
							'0,     0.3,   0.7, 0, 0 ' +
							'0,     0,     0,   1, 0" />' +
					'</filter>' +
					'<filter id="md-filter-tritanopia">' +
						'<feColorMatrix type="matrix" values="' +
							'0.95, 0.05,  0,     0, 0 ' +
							'0,    0.433, 0.567, 0, 0 ' +
							'0,    0.475, 0.525, 0, 0 ' +
							'0,    0,     0,     1, 0" />' +
					'</filter>' +
				'</defs>' +
			'</svg>';
		document.body.appendChild(container);
	}

	function createToggleButton() {
		var btn = document.createElement('button');
		btn.className = 'md-toggle';
		btn.setAttribute('type', 'button');
		btn.setAttribute('aria-label', inactiveLabel);
		btn.setAttribute('aria-pressed', 'false');
		btn.setAttribute('title', inactiveLabel);
		btn.innerHTML = '<span class="md-toggle-icon" aria-hidden="true">&#128065;</span> ' +
			'<span class="md-toggle-text">' + escapeHtml(inactiveLabel) + '</span>';
		document.body.appendChild(btn);
		return btn;
	}

	function createBadge() {
		var badge = document.createElement('div');
		badge.className = 'md-badge';
		badge.setAttribute('role', 'status');
		badge.setAttribute('aria-live', 'polite');
		badge.textContent = strings.indicador_activo || 'Colorblind mode active';
		document.body.appendChild(badge);
		return badge;
	}

	function createContrastSelector() {
		var panel = document.createElement('div');
		panel.className = 'md-contrast-selector';
		panel.setAttribute('role', 'radiogroup');
		panel.setAttribute('aria-label', 'Contrast type');

		var types = [
			{ value: 'protanopia', label: strings.protanopia || 'Protanopia' },
			{ value: 'deuteranopia', label: strings.deuteranopia || 'Deuteranopia' },
			{ value: 'tritanopia', label: strings.tritanopia || 'Tritanopia' }
		];

		for (var i = 0; i < types.length; i++) {
			var lbl = document.createElement('label');
			var radio = document.createElement('input');
			radio.type = 'radio';
			radio.name = 'md_contrast_type';
			radio.value = types[i].value;
			if (types[i].value === contrastType) {
				radio.checked = true;
			}
			lbl.appendChild(radio);
			lbl.appendChild(document.createTextNode(' ' + types[i].label));
			panel.appendChild(lbl);
		}

		document.body.appendChild(panel);
		return panel;
	}

	function escapeHtml(text) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(text));
		return div.innerHTML;
	}

	function applyFilter(type) {
		document.body.classList.remove(
			'md-active-protanopia',
			'md-active-deuteranopia',
			'md-active-tritanopia'
		);
		document.body.classList.add('md-active-' + type);
	}

	function removeFilter() {
		document.body.classList.remove(
			'md-active-protanopia',
			'md-active-deuteranopia',
			'md-active-tritanopia'
		);
	}

	function updateUI(isActive, toggleBtn, badge) {
		var label = isActive ? activeLabel : inactiveLabel;
		toggleBtn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
		toggleBtn.setAttribute('aria-label', label);
		toggleBtn.setAttribute('title', label);
		var textEl = toggleBtn.querySelector('.md-toggle-text');
		if (textEl) {
			textEl.textContent = label;
		}

		if (isActive) {
			badge.classList.add('md-visible');
			applyFilter(contrastType);
		} else {
			badge.classList.remove('md-visible');
			removeFilter();
		}
	}

	function init() {
		injectSvgFilters();

		var toggleBtn = createToggleButton();
		var badge = createBadge();
		var selector = createContrastSelector();

		var storedActive = getStorage(STORAGE_KEY_ACTIVE, null);
		var isActive;
		if (storedActive !== null) {
			isActive = storedActive === 'true';
		} else {
			isActive = initialActive;
			setStorage(STORAGE_KEY_ACTIVE, String(isActive));
		}

		var storedType = getStorage(STORAGE_KEY_TYPE, null);
		if (storedType !== null && ['protanopia', 'deuteranopia', 'tritanopia'].indexOf(storedType) !== -1) {
			contrastType = storedType;
			var radios = selector.querySelectorAll('input[type="radio"]');
			for (var r = 0; r < radios.length; r++) {
				radios[r].checked = (radios[r].value === contrastType);
			}
		}

		updateUI(isActive, toggleBtn, badge);

		if (isActive) {
			selector.classList.add('md-visible');
		}

		toggleBtn.addEventListener('click', function() {
			isActive = !isActive;
			setStorage(STORAGE_KEY_ACTIVE, String(isActive));
			updateUI(isActive, toggleBtn, badge);

			if (isActive) {
				selector.classList.add('md-visible');
			} else {
				selector.classList.remove('md-visible');
			}
		});

		selector.addEventListener('change', function(e) {
			if (e.target && e.target.name === 'md_contrast_type') {
				contrastType = e.target.value;
				setStorage(STORAGE_KEY_TYPE, contrastType);
				if (isActive) {
					applyFilter(contrastType);
				}
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
