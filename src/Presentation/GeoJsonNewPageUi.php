<?php

namespace Maps\Presentation;

class GeoJsonNewPageUi {

	private $output;

	public function __construct( OutputFacade $output ) {
		$this->output = $output;
	}

	public function addToOutput() {
		$this->output->addModules( 'ext.maps.geojson.new.page' );

		$this->output->addHtml(
			\Html::element(
				'button',
				[
					'id' => 'maps-geojson-new'
				],
				wfMessage( 'maps-geo-json-create-page-button' )->inContentLanguage()->text()
			)
		);
	}

}
