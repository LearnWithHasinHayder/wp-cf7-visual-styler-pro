jQuery(document).ready(function($) {
	// Flag to track if we're deactivating CF7 Visual Styler Pro
	let isDeactivatingPlugin = false;
	let selectedReason = '';

	// Find the deactivate link for CF7 Visual Styler Pro
	const deactivateLink = document.querySelector(
		'a[href*="action=deactivate"][href*="cf-style-pro"]'
	);

	if (deactivateLink) {
		deactivateLink.addEventListener('click', function(e) {
			e.preventDefault();
			isDeactivatingPlugin = true;
			selectedReason = '';
			showDeactivationModal();
		});
	}

	function showDeactivationModal() {
		// Create modal HTML
		const modalHTML = `
			<div id="cf7-styler-pro-modal-overlay" class="cf7-styler-pro-modal-overlay">
				<div class="cf7-styler-pro-modal">
					<button class="cf7-styler-pro-close-modal">&times;</button>
					<h2>We'd love to hear from you!</h2>
					<p>Before you go, could you tell us why you're deactivating CF7 Visual Styler Pro?</p>
					
					<form id="cf7-styler-pro-deactivation-form" class="cf7-styler-pro-form">
						<div class="cf7-styler-pro-options">
							<label class="cf7-styler-pro-option">
								<input type="radio" name="reason" value="pricing" />
								<span>Pricing issue</span>
							</label>
							
							<label class="cf7-styler-pro-option">
								<input type="radio" name="reason" value="support" />
								<span>Support issue</span>
							</label>
							
							<label class="cf7-styler-pro-option">
								<input type="radio" name="reason" value="bugs" />
								<span>Bugs or issues</span>
							</label>
							
							<label class="cf7-styler-pro-option">
								<input type="radio" name="reason" value="better_alternative" />
								<span>Found something better</span>
							</label>
							
							<label class="cf7-styler-pro-option">
								<input type="radio" name="reason" value="not_needed" />
								<span>Not needed anymore</span>
							</label>
							
							<label class="cf7-styler-pro-option">
								<input type="radio" name="reason" value="other" />
								<span>Other</span>
							</label>
						</div>

						<div id="cf7-styler-pro-details-field" class="cf7-styler-pro-details" style="display: none;">
							<textarea 
								name="details" 
								placeholder="Please tell us more..." 
								maxlength="500"
								rows="4"
							></textarea>
							<small><span id="cf7-styler-pro-char-count">0</span>/500</small>
						</div>

						<div class="cf7-styler-pro-actions">
							<button type="submit" class="button button-primary">Submit &amp; Deactivate</button>
							<button type="button" class="button cf7-styler-pro-skip-btn">Skip &amp; Deactivate</button>
						</div>
					</form>
				</div>
			</div>
		`;

		// Add styles
		const styleHTML = `
			<style>
				.cf7-styler-pro-modal-overlay {
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					background: rgba(0, 0, 0, 0.5);
					display: flex;
					align-items: center;
					justify-content: center;
					z-index: 99999;
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
				}

				.cf7-styler-pro-modal {
					background: white;
					border-radius: 8px;
					box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
					max-width: 500px;
					width: 90%;
					padding: 40px;
					position: relative;
					animation: cf7-styler-pro-slide-in 0.3s ease-out;
				}

				@keyframes cf7-styler-pro-slide-in {
					from {
						opacity: 0;
						transform: translateY(-50px);
					}
					to {
						opacity: 1;
						transform: translateY(0);
					}
				}

				.cf7-styler-pro-modal h2 {
					margin: 0 0 10px 0;
					font-size: 20px;
					font-weight: 600;
					color: #333;
				}

				.cf7-styler-pro-modal > p {
					margin: 0 0 20px 0;
					color: #666;
					font-size: 14px;
				}

				.cf7-styler-pro-close-modal {
					position: absolute;
					top: 15px;
					right: 15px;
					background: none;
					border: none;
					font-size: 28px;
					cursor: pointer;
					color: #999;
					padding: 0;
					width: 28px;
					height: 28px;
					display: flex;
					align-items: center;
					justify-content: center;
				}

				.cf7-styler-pro-close-modal:hover {
					color: #333;
				}

				.cf7-styler-pro-options {
					display: flex;
					flex-direction: column;
					gap: 12px;
					margin-bottom: 20px;
				}

				.cf7-styler-pro-option {
					display: flex;
					align-items: center;
					cursor: pointer;
					padding: 10px 12px;
					border-radius: 4px;
					border: 1px solid #ddd;
					transition: all 0.2s ease;
				}

				.cf7-styler-pro-option:hover {
					background: #f5f5f5;
					border-color: #999;
				}

				.cf7-styler-pro-option input[type="radio"] {
					margin-right: 12px;
					cursor: pointer;
					accent-color: #0073aa;
				}

				.cf7-styler-pro-option input[type="radio"]:checked + span {
					font-weight: 600;
					color: #0073aa;
				}

				.cf7-styler-pro-option input[type="radio"]:checked {
					--wp-admin-theme-color: #0073aa;
				}

				.cf7-styler-pro-option span {
					flex: 1;
					color: #333;
					font-size: 14px;
				}

				.cf7-styler-pro-details {
					margin-bottom: 20px;
					padding: 12px;
					background: #f9f9f9;
					border-radius: 4px;
					border: 1px solid #e0e0e0;
				}

				.cf7-styler-pro-details textarea {
					width: 100%;
					padding: 8px;
					border: 1px solid #ddd;
					border-radius: 4px;
					font-family: inherit;
					font-size: 13px;
					resize: vertical;
				}

				.cf7-styler-pro-details small {
					display: block;
					margin-top: 5px;
					color: #999;
					font-size: 12px;
					text-align: right;
				}

				.cf7-styler-pro-actions {
					display: flex;
					gap: 10px;
					justify-content: flex-end;
				}

				.cf7-styler-pro-actions button {
					padding: 8px 16px !important;
					font-size: 14px !important;
					height: auto !important;
					border-radius: 4px !important;
				}

				.cf7-styler-pro-skip-btn {
					background: #f3f3f3 !important;
					color: #333 !important;
					border: 1px solid #ddd !important;
				}

				.cf7-styler-pro-skip-btn:hover {
					background: #e0e0e0 !important;
				}
			</style>
		`;

		// Inject modal and styles
		$('body').append(styleHTML + modalHTML);

		// Event listeners
		const modal = document.getElementById('cf7-styler-pro-modal-overlay');
		const closeBtn = document.querySelector('.cf7-styler-pro-close-modal');
		const skipBtn = document.querySelector('.cf7-styler-pro-skip-btn');
		const form = document.getElementById('cf7-styler-pro-deactivation-form');
		const reasonRadios = document.querySelectorAll('input[name="reason"]');
		const detailsField = document.getElementById('cf7-styler-pro-details-field');
		const detailsTextarea = document.querySelector('.cf7-styler-pro-details textarea');
		const charCount = document.getElementById('cf7-styler-pro-char-count');

		// Show/hide details field based on selected reason
		reasonRadios.forEach(radio => {
			radio.addEventListener('change', function() {
				selectedReason = this.value;
				if (this.value === 'other') {
					detailsField.style.display = 'block';
				} else {
					detailsField.style.display = 'none';
				}
			});
		});

		// Update character count
		detailsTextarea.addEventListener('input', function() {
			charCount.textContent = this.value.length;
		});

		// Close modal
		closeBtn.addEventListener('click', closeModal);

		function closeModal() {
			modal.remove();
			isDeactivatingPlugin = false;
		}

		// Handle form submission
		form.addEventListener('submit', function(e) {
			e.preventDefault();

			const formData = new FormData(form);
			const reason = formData.get('reason');
			const details = formData.get('details') || '';

			if (!reason) {
				alert('Please select a reason');
				return;
			}

			// Send data via AJAX
			$.ajax({
				type: 'POST',
				url: cf7StylerProDeactivation.ajax_url,
				data: {
					action: 'cf7_styler_pro_deactivation_reason',
					nonce: cf7StylerProDeactivation.nonce,
					reason: reason,
					details: details,
				},
				success: function() {
					// Proceed with actual deactivation
					//window.location.href = deactivateLink.href;
				},
				error: function() {
					// Even on error, proceed with deactivation
					//window.location.href = deactivateLink.href;
				},
			});
		});

		// Skip button
		skipBtn.addEventListener('click', function() {
			window.location.href = deactivateLink.href;
		});
	}
});
