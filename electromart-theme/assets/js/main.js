jQuery(function ($) {
	'use strict';

	var cfg = window.EM_Config || {};

	function esc(str) {
		return String(str == null ? '' : str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function post(action, data) {
		return $.post(cfg.ajax_url, $.extend({ action: action, nonce: cfg.nonce }, data || {}));
	}

	// ۱. منوی موبایل و سبد خرید کشویی
	$('.em-mobile-menu-toggle').on('click', function () {
		var open = $('.em-main-nav').toggleClass('mobile-open').hasClass('mobile-open');
		$(this).attr('aria-expanded', open ? 'true' : 'false');
	});

	function closeAllOverlays() {
		$('.em-drawer, .em-modal, #em-backdrop').removeClass('open');
		$('body').removeClass('em-no-scroll');
	}

	function openOverlay(sel) {
		$(sel + ', #em-backdrop').addClass('open');
		$('body').addClass('em-no-scroll');
	}

	$('.em-cart-trigger').on('click', function () {
		openOverlay('#em-mini-cart-drawer');
	});

	$(document).on('click', '.em-close-drawer, .em-modal-close, #em-backdrop', closeAllOverlays);
	$(document).on('keyup', function (e) {
		if (e.key === 'Escape') closeAllOverlays();
	});

	$(document.body).on('added_to_cart', function () {
		$('#em-quickview-modal').removeClass('open');
		openOverlay('#em-mini-cart-drawer');
	});

	// ۲. جستجوی زنده (خروجی سرور escape می‌شود تا XSS ممکن نباشد)
	var searchTimer = null;
	var searchXhr = null;
	var $dropdown = $('#em-search-results');

	$('#em-live-search-input').on('input', function () {
		clearTimeout(searchTimer);
		var keyword = $(this).val().trim();

		if (keyword.length < 2) {
			$dropdown.hide().empty();
			return;
		}

		searchTimer = setTimeout(function () {
			if (searchXhr) searchXhr.abort();
			searchXhr = post('em_live_search', { keyword: keyword }).done(function (res) {
				if (res && res.success && res.data.length) {
					var html = res.data.map(function (item) {
						return '<a href="' + esc(item.url) + '" class="em-search-item">' +
							'<img src="' + esc(item.image) + '" alt="">' +
							'<div><div class="em-search-title">' + esc(item.title) + '</div>' +
							// قیمت HTML آماده ووکامرس است که سمت سرور با wp_kses_post پاک‌سازی شده
							'<div class="em-search-price">' + item.price + '</div></div></a>';
					}).join('');
					$dropdown.html(html).show();
				} else {
					$dropdown.html('<div class="em-search-empty">' + esc(cfg.i18n.noResults) + '</div>').show();
				}
			});
		}, 300);
	});

	$(document).on('click', function (e) {
		if (!$(e.target).closest('.em-search-wrapper').length) $dropdown.hide();
	});

	// ۳. مشاهده سریع
	$(document).on('click', '.em-quickview-btn', function () {
		var productId = $(this).data('id');
		$('#em-quickview-body').html('<p class="em-loading">' + esc(cfg.i18n.loading) + '</p>');
		openOverlay('#em-quickview-modal');

		post('em_quick_view', { product_id: productId }).done(function (res) {
			if (res && res.success) {
				$('#em-quickview-body').html(res.data.html);
			}
		});
	});

	// ۴. علاقه‌مندی‌ها: وضعیت دکمه‌ها بعد از بارگذاری صفحه هم نشان داده می‌شود
	function paintWishlist(ids) {
		ids = (ids || []).map(Number);
		$('.em-wishlist-count').text(ids.length);
		$('.em-wishlist-btn').each(function () {
			$(this).toggleClass('active', ids.indexOf(Number($(this).data('id'))) !== -1);
		});
	}

	post('em_get_wishlist').done(function (res) {
		if (res && res.success) paintWishlist(res.data.ids);
	});

	$(document).on('click', '.em-wishlist-btn', function () {
		var $btn = $(this);
		$btn.addClass('is-loading');
		post('em_toggle_wishlist', { product_id: $btn.data('id') }).done(function (res) {
			if (res && res.success) paintWishlist(res.data.ids);
		}).always(function () {
			$btn.removeClass('is-loading');
		});
	});

	// ۵. مقایسه محصولات (LocalStorage + جدول AJAX)
	function getCompareList() {
		try {
			var list = JSON.parse(localStorage.getItem('em_compare_ids') || '[]');
			return Array.isArray(list) ? list.map(Number).filter(Boolean) : [];
		} catch (e) {
			return [];
		}
	}

	function saveCompareList(list) {
		try {
			localStorage.setItem('em_compare_ids', JSON.stringify(list));
		} catch (e) {}
		$('.em-compare-count').text(list.length);
		$('.em-compare-btn').each(function () {
			$(this).toggleClass('active', list.indexOf(Number($(this).data('id'))) !== -1);
		});
	}

	saveCompareList(getCompareList());

	function openCompareModal() {
		$('#em-compare-body').html('<p class="em-loading">' + esc(cfg.i18n.loading) + '</p>');
		openOverlay('#em-compare-modal');

		post('em_compare_table', { ids: getCompareList() }).done(function (res) {
			if (res && res.success) {
				$('#em-compare-body').html(res.data.html);
			}
		});
	}

	$(document).on('click', '.em-compare-btn', function () {
		var id = Number($(this).data('id'));
		var list = getCompareList();
		if (list.indexOf(id) === -1) {
			if (list.length >= 4) list.shift();
			list.push(id);
			saveCompareList(list);
		}
		openCompareModal();
	});

	$('.em-open-compare-modal').on('click', openCompareModal);

	$(document).on('click', '.em-remove-compare', function () {
		var id = Number($(this).data('id'));
		saveCompareList(getCompareList().filter(function (item) { return item !== id; }));
		openCompareModal();
	});

	// ۶. فیلتر و مرتب‌سازی AJAX با صفحه‌بندی
	var $form = $('#em-ajax-filter-form');
	var $grid = $('#em-products-container');
	var filterXhr = null;

	function triggerAjaxFilter(page) {
		if (!$form.length) return;
		if (filterXhr) filterXhr.abort();

		$grid.addClass('is-loading');
		filterXhr = post('em_filter_products', {
			category: $form.find('[name="category"]').val() || '',
			brand: $form.find('[name="brand"]').val() || '',
			max_price: $form.find('[name="max_price"]').val() || '',
			in_stock: $form.find('[name="in_stock"]').is(':checked') ? '1' : '0',
			orderby: $('#em-sort-select').val(),
			paged: page || 1
		}).done(function (res) {
			if (res && res.success) {
				$grid.html(res.data.html);
				$('#em-pagination').html(res.data.pagination);
				post('em_get_wishlist').done(function (r) {
					if (r && r.success) paintWishlist(r.data.ids);
				});
				saveCompareList(getCompareList());
				if (page && page > 1) {
					$('html, body').animate({ scrollTop: $grid.offset().top - 120 }, 300);
				}
			}
		}).always(function () {
			$grid.removeClass('is-loading');
		});
	}

	$form.on('submit', function (e) {
		e.preventDefault();
		triggerAjaxFilter(1);
	});

	$('#em-sort-select').on('change', function () {
		triggerAjaxFilter(1);
	});

	$(document).on('click', '.em-ajax-pagination a[data-page]', function (e) {
		e.preventDefault();
		triggerAjaxFilter(parseInt($(this).data('page'), 10));
	});

	// ۷. تب‌های صفحه محصول
	$('.em-tab-trigger').on('click', function () {
		var target = $(this).data('target');
		$('.em-tab-trigger, .em-tab-panel').removeClass('active');
		$(this).addClass('active');
		$('#' + target).addClass('active');
	});

	// ۸. خبرنامه
	$('#em-newsletter-form').on('submit', function (e) {
		e.preventDefault();
		var $f = $(this);
		var $msg = $('.em-newsletter-msg');
		post('em_newsletter', { email: $f.find('[name="email"]').val() }).done(function (res) {
			var ok = res && res.success;
			$msg.toggleClass('is-error', !ok).text(res && res.data ? res.data.message : '');
			if (ok) $f[0].reset();
		});
	});
});
