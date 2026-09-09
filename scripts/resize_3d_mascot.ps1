Add-Type -AssemblyName System.Drawing
$srcPath = "d:\Work\macatung\public\brand\decode\mascot-ma-giai-ma-3d.jpg"
$img = [System.Drawing.Image]::FromFile($srcPath)

$sizes = @(
  @{ w = 1024; h = 1024; name = "mascot-ma-giai-ma-3d.png" },
  @{ w = 512;  h = 512;  name = "decode-avatar-3d-512.png" },
  @{ w = 256;  h = 256;  name = "decode-avatar-3d-256.png" },
  @{ w = 128;  h = 128;  name = "decode-avatar-3d-128.png" }
)

foreach ($s in $sizes) {
  $bmp = New-Object System.Drawing.Bitmap $s.w, $s.h
  $g = [System.Drawing.Graphics]::FromImage($bmp)
  $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
  $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
  $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
  $g.DrawImage($img, 0, 0, $s.w, $s.h)
  $outPath = Join-Path "d:\Work\macatung\public\brand\decode" $s.name
  $bmp.Save($outPath, [System.Drawing.Imaging.ImageFormat]::Png)
  $g.Dispose()
  $bmp.Dispose()
  Write-Host "Generated: $($s.name)"
}
$img.Dispose()
