jQuery(document).ready(function ($) {
	'use strict';

	// ۱. منوی موبایل و سبد خرید کشویی (Mini Cart)
	$('.em-mobile-menu-toggle').on('click', function () {
		$('.em-main-nav').toggleClass('mobile-open');
	});

	function closeAllOverlays() {
		$('.em-drawer, .em-modal, #em-backdrop').removeClass('open');
	}

	$('.em-cart-trigger').on('click', function () {
		$('#em-mini-cart-drawer, #em-backdrop').addClass('open');
	});

	$('.em-close-drawer, .em-modal-close, #em-backdrop').on('click', closeAllOverlays);

	// باز شدن خودکار Mini Cart پس از افزودن محصول با AJAX
	$(document.body).on('added_to_cart', function () {
		$('#em-mini-cart-drawer, #em-backdrop').addClass('open');
	});

	// ۲. جستجوی زنده AJAX (Live Search)
	let searchTimer = null;
	$('#em-live-search-input').on('keyup', function () {
		clearTimeout(searchTimer);
		const keyword = $(this).val().trim();
		const $dropdown = $('#em-search-results');

		if (keyword.length < 2) {
			$dropdown.hide().empty();
			return;
		}

		searchTimer = setTimeout(function () {
			$.post(EM_Config.ajax_url, {
				action: 'em_live_search',
				nonce: EM_Config.nonce,
				keyword: keyword
			}, function (res) {
				if (res.success && res.data.length > 0) {
					let html = '';
					res.data.forEach(function (item) {
						html += `
							<a href="${item.url}" class="em-search-item">
								<img src="${item.image}" alt="${item.title}">
								<div>
									<div style="font-weight:700;font-size:0.86rem;">${item.title}</div>
									<div style="font-size:0.8rem;color:#0052FF;">${item.price}</div>
								</div>
							</a>
						`;
					});
					$dropdown.html(html).show();
				} else {
					$dropdown.html('<div style="padding:14px;font-size:0.85rem;color:#6B7280;">محصولی یافت نشد.</div>').show();
				}
			});
		}, 300);
	});

	// ۳. مشاهده سریع محصول (Quick View AJAX)
	$(document).on('click', '.em-quickview-btn', function () {
		const productId = $(this).data('id');
		$('#em-quickview-body').html('<p style="text-align:center;padding:30px;">در حال بارگذاری مشخصات محصول...</p>');
		$('#em-quickview-modal, #em-backdrop').addClass('open');

		$.post(EM_Config.ajax_url, {
			action: 'em_quick_view',
			nonce: EM_Config.nonce,
			product_id: productId
		}, function (res) {
			if (res.success) {
				$('#em-quickview-body').html(res.data.html);
			}
		});
	});

	// ۴. علاقه‌مندی‌ها (Wishlist AJAX)
	$(document).on('click', '.em-wishlist-btn', function () {
		const $btn = $(this);
		const productId = $btn.data('id');

		$.post(EM_Config.ajax_url, {
			action: 'em_toggle_wishlist',
			nonce: EM_Config.nonce,
			product_id: productId
		}, function (res) {
			if (res.success) {
				$btn.toggleClass('active', res.data.status === 'added');
			}
		});
	});

	// ۵. سیستم مقایسه محصولات (Compare List با LocalStorage + AJAX Table)
	function getCompareList() {
		try {
			return JSON.parse(localStorage.getItem('em_compare_ids') || '[]');
		} catch (e) {
			return [];
		}
	}

	function saveCompareList(list) {
		localStorage.setItem('em_compare_ids', JSON.stringify(list));
		$('.em-compare-count').text(list.length);
	}

	saveCompareList(getCompareList());

	$(document).on('click', '.em-compare-btn', function () {
		const id = parseInt($(this).data('id'), 10);
		let list = getCompareList();
		if (!list.includes(id)) {
			if (list.length >= 4) {
				list.shift();
			}
			list.push(id);
			saveCompareList(list);
		}
		openCompareModal();
	});

	$('.em-open-compare-modal').on('click', openCompareModal);

	$(document).on('click', '.em-remove-compare', function () {
		const id = parseInt($(this).data('id'), 10);
		const list = getCompareList().filter(item => item !== id);
		saveCompareList(list);
		openCompareModal();
	});

	function openCompareModal() {
		const list = getCompareList();
		$('#em-compare-body').html('<p style="padding:20px;">در حال آماده‌سازی جدول مقایسه...</p>');
		$('#em-compare-modal, #em-backdrop').addClass('open');

		$.post(EM_Config.ajax_url, {
			action: 'em_compare_table',
			nonce: EM_Config.nonce,
			ids: list
		}, function (res) {
			if (res.success) {
				$('#em-compare-body').html(res.data.html);
			}
		});
	}

	// ۶. فیلتر و مرتب‌سازی AJAX محصولات بدون رفرش صفحه
	function triggerAjaxFilter() {
		const $form = $('#em-ajax-filter-form');
		if (!$form.length) return;

		const data = {
			action: 'em_filter_products',
			nonce: EM_Config.nonce,
			category: $form.find('[name="category"]').val(),
			brand: $form.find('[name="brand"]').val(),
			max_price: $form.find('[name="max_price"]').val(),
			in_stock: $form.find('[name="in_stock"]').is(':checked') ? '1' : '0',
			orderby: $('#em-sort-select').val()
		};

		$('#em-products-container').css('opacity', '0.45');
		$.post(EM_Config.ajax_url, data, function (res) {
			$('#em-products-container').css('opacity', '1');
			if (res.success) {
				$('#em-products-container').html(res.data.html);
			}
		});
	}

	$('#em-ajax-filter-form').on('submit', function (e) {
		e.preventDefault();
		triggerAjaxFilter();
	});

	$('#em-sort-select').on('change', triggerAjaxFilter);

	// ۷. تب‌های صفحه اختصاصی محصول
	$('.em-tab-trigger').on('click', function () {
		const target = $(this).data('target');
		$('.em-tab-trigger, .em-tab-panel').removeClass('active');
		$(this).addClass('active');
		$('#' + target).addClass('active');
	});
});

