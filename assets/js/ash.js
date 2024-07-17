/**
 * Clickable elements
 */
document.getElementsByClassName('clickable').on( 'click', (event) => {
	const tgt = event.target;
	const href = tgt.getAttribute('href');

	if ( href === null || href === undefined ) {

			event.preventDefault();
			event.stopPropagation();

			const anchor    = tgt.querySelector('a:first-of-type');
			const btn       = tgt.querySelector('button:first-of-type');
			const uri = anchor ? anchor.getAttribute('href') : null;
			const newWindow = tgt.classList.contains('external-link') || (anchor && anchor.classList.contains('external-link')) || (anchor && anchor.getAttribute( 'target' ) === '_blank') || event.metaKey || event.ctrlKey;

		if ( btn ) {
			event.stopImmediatePropagation();
			btn.click();
			return;
		}

		if ( ! btn && anchor ) {
			if ( newWindow ) {
				window.open( uri );
			} else {
				window.location = uri;
			}
		}

		return false;
	} else {
		return;
	}
});