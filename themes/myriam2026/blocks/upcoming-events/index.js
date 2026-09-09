( function () {
	var registerBlockType = wp.blocks.registerBlockType;
	var createElement     = wp.element.createElement;
	var Fragment          = wp.element.Fragment;
	var ServerSideRender  = wp.serverSideRender.default || wp.serverSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody         = wp.components.PanelBody;
	var TextControl       = wp.components.TextControl;

	registerBlockType( 'myriam2026/upcoming-events', {
		edit: function ( props ) {
			var attributes    = props.attributes;
			var setAttributes = props.setAttributes;

			return createElement(
				Fragment,
				{},
				createElement(
					InspectorControls,
					{},
					createElement(
						PanelBody,
						{ title: 'Section Settings' },
						createElement( TextControl, {
							label: 'Section label',
							value: attributes.sectionLabel,
							onChange: function ( value ) {
								setAttributes( { sectionLabel: value } );
							},
						} )
					)
				),
				createElement( ServerSideRender, {
					block: 'myriam2026/upcoming-events',
					attributes: attributes,
				} )
			);
		},
	} );
} )();
