<?php

// Unsure if this changes between client updates or what. It's a base64 encoded string which contains two MD5 hashes delimited by a colon.
// The hashes are derived before any authentication so it might be a checksum of some file?
const EPIC_LAUNCHER_AUTHORIZATION   = "MzRhMDJjZjhmNDQxNGUyOWIxNTkyMTg3NmRhMzZmOWE6ZGFhZmJjY2M3Mzc3NDUwMzlkZmZlNTNkOTRmYzc2Y2Y=";

// Same format as the EPIC_LAUNCHER_AUTHORIZATION. Also unsure of it's origin.
const FORTNITE_AUTHORIZATION        = "ZWM2ODRiOGM2ODdmNDc5ZmFkZWEzY2IyYWQ4M2Y1YzY6ZTFmMzFjMjExZjI4NDEzMTg2MjYyZDM3YTEzZmM4NGQ=";