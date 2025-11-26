# Assets Folder

This folder contains game assets (images and audio).

## Current Status: OPTIONAL

The game **works perfectly without these assets**! CSS gradients are used as tile backgrounds.

---

## Images Folder

### /images/tiles/ (Optional)
If you want custom tile images instead of gradients, add 15 images here:
- `tile-1.jpg` through `tile-15.jpg`
- Recommended size: 200×200px
- Format: JPG or PNG
- Theme: Christmas (ornaments, candy canes, snowflakes, presents, bells, etc.)

**Free image sources:**
- Unsplash: https://unsplash.com/s/photos/christmas
- Pixabay: https://pixabay.com/images/search/christmas/
- Pexels: https://www.pexels.com/search/christmas/

### /images/backgrounds/ (Optional)
Background images for themes (not required):
- `workshop.jpg` - Santa's workshop scene
- `snowy.jpg` - Snowy winter night
- `reindeer.jpg` - Reindeer in forest

---

## Audio Folder

### /audio/ (Optional)
If you want sound effects, add these files:
- `bgm.mp3` - Background music (looping Christmas music)
- `tile-click.mp3` - Click sound when selecting tile
- `move.mp3` - Whoosh sound when tile moves
- `victory.mp3` - Celebration jingle when puzzle solved
- `powerup.mp3` - Sound effect when using power-up

**Free audio sources:**
- YouTube Audio Library: https://www.youtube.com/audiolibrary
- Freesound: https://freesound.org/
- Zapsplat: https://www.zapsplat.com/

---

## Important Notes

1. **Game works without assets** - CSS gradients create colorful tiles
2. **Audio is muted by default** - Volume controls in game UI
3. **Images load if available** - Graceful fallback to gradients
4. **File names matter** - Must match exact names listed above
5. **Keep file sizes small** - < 100KB per image, < 1MB per audio

---

## Quick Setup (Optional)

If you want to add assets quickly:

### Option 1: Use Emojis (Instant)
Already done! Tiles show numbers with gradient backgrounds.

### Option 2: Use Online Images (5 minutes)
1. Search for Christmas images on Unsplash
2. Download 15 images
3. Rename to tile-1.jpg through tile-15.jpg
4. Copy to `/assets/images/tiles/`

### Option 3: Use Audio (10 minutes)
1. Download Christmas music from YouTube Audio Library
2. Download sound effects from Freesound
3. Rename files as listed above
4. Copy to `/assets/audio/`

---

## Asset Integration

The game automatically checks for assets:
- If image exists: Uses image as tile background
- If image missing: Uses CSS gradient (current default)
- If audio exists: Plays sounds
- If audio missing: Silent (no errors)

**No code changes needed** - Just drop files in the right folders!

---

**Bottom line:** Assets are completely optional. Your game looks great and works perfectly without them! 🎄✨
