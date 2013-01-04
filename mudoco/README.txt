MuDoCo - A Multi Domain Cookie PHP Script

A little (poc) script too help you handle cookie/session over multiple domain.

Is has a basic security layer (nonce) and it is pluggable.

Source tree :

- inc/ : MuDoCo classes
- mudoco/server/ : the central server to hold cookie/session
- mudoco/client/ : some dropin files to put on the client website server
- testsite/ : a basic test site
- plugins/ : a dummy plugin