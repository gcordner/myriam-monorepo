( function () {
	var registerBlockType = wp.blocks.registerBlockType;
	var createElement = wp.element.createElement;
	var ServerSideRender = wp.serverSideRender.default || wp.serverSideRender;

	registerBlockType( 'myriam2026/upcoming-events', {
		edit: function () {
			return createElement( ServerSideRender, {
				block: 'myriam2026/upcoming-events',
			} );
		},
	} );
} )();
