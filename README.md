# RoboID (php-robo-id)

## Format
A RoboID has two parts, one time based (T) and one random (R) part.
The time based part is based on the Unix timestamp and consists of 45 bits.
The random part consists of 30 bits (short version) or 80 bits (long version).

The long version will be uuid compatible. That means, that in the random part the bits 0-3 (uuid version) are set to 'B' and the bits 16-17 (uuid variant) are set to 10b.

There are three different string representations and one as json.
Every implementation needs to accept and export the json representation.

### HEX
TTTTTTTTTTTT-RRRRRRRR
TTTTTTTTTTTT-RRRRRRRRRRRRRRRRRRRR
The time based part is left padded with zeros to 48 bits.
The random part is right padded with zeros to 32 or 80 bits.

### Base32
TTTTTTTTT-RRRRRR
TTTTTTTTT-RRRRRRRRRRRRRRRR
The time based and the random part are encoded using base32 crockford charset.
No padding is necessary using this representation

### UUID
AAAAAAAA-BBBB-VCCC-WDDD-EEEEEEEEEEEE
A and B together build the time based part. The time based part is left padded with 100b to 48 bits. This ensures that the ID starts with a character different than '0'.
V is hard coded to 'B'.
In long version C to E together build the random part.
In short version C and D are filled with zeros. The random part is encoded in E, right padded with zeros.

### JSON
{
  "e": "hex|b32|b64",     // encoding
  "v": "S|L",             // version
  "t": "time based part",
  "r": "random part"
}
