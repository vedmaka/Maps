<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use FormatJson;
use Html;

class MapHtmlBuilder {

	public function getMapHTML( array $params, string $mapName, string $serviceName ): string {
		return '<div id="' . $mapName . '" class="maps-leaflet" style="height: 400px; width: 800px"></div>';

	}

}
