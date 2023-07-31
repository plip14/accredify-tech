
<html>
    <head>
        <title>Post page</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">        
    </head>
    <body>        
        <form name="text-post-form" id="text-post-form" method="post" action="{{url('verify')}}" >
         @csrf 
         <div>
            <label for="load">Unload your cargo below</label>
            <div>
                <textarea name="load" id="load" rows="25" cols="80">{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}</textarea>
            </div>
         </div>
         <div>
            <input type="submit" value="Submit">
         </div>
        </form>
        <form name="file-post-form" id="file-post-form" method="post" action="{{url('verify')}}" enctype="multipart/form-data">
         @csrf 
         <div>
            <label for="load">Or upload a file</label>
            <div>
              <input type="file" name="oaFile">
            </div>
          </div>
          <div>
            <input type="submit" value="Submit">
         </div>
        </form>
        <div>

            Result
            <div id="result"></div>
        </div>
        <div>Log table:
            <iframe src="{{url('log')}}" height="500" width="100%" id="logtable"></iframe>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>        
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
                var textForm=document.querySelector('#text-post-form');
                var fileForm=document.querySelector('#file-post-form');
                var config = {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector("meta[name=csrf-token]").getAttribute('content')
                        }
                    }
                var updateResult = (response) => {
                  document.getElementById('result').innerHTML=JSON.stringify(response.data);
                  document.getElementById('logtable').src = document.getElementById('logtable').src;
                }
                textForm.addEventListener('submit', (e) => {
                    e.preventDefault();                    
                    axios.post(textForm.action, textForm, config)
                    .then((response) => updateResult(response));
                });

                fileForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    axios.post(fileForm.action, fileForm, config)
                    .then((response) => updateResult(response));
                });

        </script>
    </body>
</html>