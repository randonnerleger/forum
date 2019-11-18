<?php
require '../../configRL.php';
define('PUN_ROOT', '../' );
require PUN_ROOT.'include/common.php';

$pun_user_guest = ($pun_user['is_guest'] ? true : false );
define('pun_user_guest', $pun_user_guest);

$GLOBALS['punname'] 	= $pun_user['username'];
$GLOBALS['punid'] 		= $pun_user['id'];
$GLOBALS['punusergroup'] 	= $pun_user['g_id'];

// Fotoo Hosting single-file release version 2.1.1
?><?php

// French translations
if (file_exists(dirname(__FILE__) . '/user_config.php'))
{
    require dirname(__FILE__) . '/user_config.php';
}

if (!function_exists('__')) {
	function __($str) {
	    return $str;
	}
}

if (isset($_GET["js"])): header("Content-Type: text/javascript"); ?>
(function () {
    if (!Array.prototype.indexOf)
    {
        Array.prototype.indexOf = function(elt /*, from*/)
        {
            var len = this.length >>> 0;

            var from = Number(arguments[1]) || 0;
            from = (from < 0) ? Math.ceil(from) : Math.floor(from);

            if (from < 0)
            {
                from += len;
            }

            for (; from < len; from++)
            {
                if (from in this && this[from] === elt)
                    return from;
            }

            return -1;
        };
    }

    var can_submit = true;
    var last_filename = '';
    var loading_gif = 'data:image/gif;base64,R0lGODlhEAAQAPIAAP%2F%2F%2FwAAAMLCwkJCQgAAAGJiYoKCgpKSkiH%2BGkNyZWF0ZWQgd2l0aCBhamF4bG9hZC5pbmZvACH5BAAKAAAAIf8LTkVUU0NBUEUyLjADAQAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa%2BdIAAAh%2BQQACgABACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkEAAoAAgAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkEAAoAAwAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo%2FIpHI5TAAAIfkEAAoABAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo%2FIpFKSAAAh%2BQQACgAFACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh%2BQQACgAGACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAAKAAcALAAAAAAQABAAAAMyCLrc%2FjDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA%3D%3D';
    var album_id = null;
    var album_check = null;
    var xhr = new XMLHttpRequest;

    function cleanFileName(filename)
    {
        filename = filename.replace(/[\\\\]/g, "/");
        filename = filename.split("/");
        filename = filename[filename.length - 1];
        filename = filename.split(".");
        filename = filename[0];
        filename = filename.replace(/\s+/g, "-");
        filename = filename.replace(/[^a-zA-Z0-9_.-]/ig, "");
        filename = filename.substr(0, 30);
        filename = filename.replace(/(^[_.-]+|[_.-]+$)/g, "");
        return filename;
    }

    function uploadPicture(index, element_index)
    {
        var file = document.getElementById('f_files').files[index];

        if (!(/^image\/jpe?g$/i.test(file.type)))
        {
            uploadPicture(index+1, element_index);
            return;
        }

        var current = document.getElementById('albumParent').childNodes[element_index];
        var resized_img = document.createElement('div');
        resized_img.style.display = "none";

        var name = current.getElementsByTagName('input')[0];
        name.disabled = true;

        var progress = document.createElement('span');
        current.appendChild(progress);

        resize(
            file,
            config.max_width,
            resized_img,
            progress,
            function()
            {
                var img = resized_img.firstChild

                progress.innerHTML = "<?php echo __('Uploading'); ?>... <img class=\"loading\" src=\"" + loading_gif + "\" alt=\"\" />";

                var params = "album_append=1&name=" + encodeURIComponent(name.value) + "&album=" + encodeURIComponent(album_id);
                params += "&filename=" + encodeURIComponent(file.name);
                params += "&content=" + encodeURIComponent(img.src.substr(img.src.indexOf(',') + 1));

                xhr.open('POST', config.base_url + '?album', true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.setRequestHeader("Content-length", params.length);
                xhr.setRequestHeader("Connection", "close");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200)
                    {
                        progress.innerHTML = "<?php echo __('Uploaded'); ?> <b>&#10003;</b>";
                        img.parentNode.removeChild(img);

                        if (index + 1 < document.getElementById('f_files').files.length)
                        {
                            uploadPicture(index+1, element_index+1);
                        }
                        else
                        {
                            location.href = config.album_page_url + album_id + (config.album_page_url.indexOf('?') ? '&c=' : '?c=') + album_check;
                        }
                    }
                };

                xhr.send(params);
                params = null;
            }
        );
    }

    window.onload = function ()
    {
        if (!FileReader && !window.URL)
            return false;

        if (FileList && XMLHttpRequest && document.getElementById('albums') == null )
        {
			var album_li = document.createElement('li');
			album_li.setAttribute("id", "albums");
            var album_a = document.createElement('a');
            album_a.href = '?album';
            album_a.innerHTML = '<?php echo __('Upload an album'); ?>';
            album_li.appendChild(album_a);
            var link = document.querySelector('#brdmenu ul li:nth-child(2)');
            link.parentNode.insertBefore(album_li, link);
			sessionStorage.setItem("album_enabled", "ok");
        }

        document.getElementById('f_submit').style.display = 'none';

        var parent = document.getElementById('albumParent');

        // Mode album
        if (parent)
        {
            document.getElementById("f_files").onchange = function ()
            {
                if (this.files.length < 1)
                {
                    return false;
                }

                if (parent.firstChild && parent.firstChild.nodeType == Node.TEXT_NODE)
                {
                    parent.removeChild(parent.firstChild);
                }

                var found = new Array;
                var to_resize = new Array;

                for (var i = 0; i < this.files.length; i++)
                {
                    var file = this.files[i];

                    if (!(/^image\/jpe?g$/i.test(file.type)))
                    {
                        continue;
                    }

                    var id = encodeURIComponent(file.name + file.type + file.size);

                    if (document.getElementById(id))
                    {
                        found.push(id);
                        continue;
                    }

                    var fig = document.createElement('figure');
                    fig.id = id;
                    var caption = document.createElement('figcaption');
                    var name = document.createElement('input');
                    name.type = 'text';
                    name.value = cleanFileName(file.name);

                    var thumb = document.createElement('div');
                    thumb.className = 'thumb';

                    var progress = document.createElement('p');

                    caption.appendChild(name);
                    fig.appendChild(thumb);
                    fig.appendChild(progress);
                    fig.appendChild(caption);
                    parent.appendChild(fig);

                    to_resize.push(new Array(file, thumb, progress));
                    found.push(id);
                }

                var l = parent.childNodes.length;
                for (var i = l - 1; i >= 0; i--)
                {
                    if (found.indexOf(parent.childNodes[i].id) == -1)
                    {
                        parent.removeChild(parent.childNodes[i]);
                    }
                }

                function resizeFromList()
                {
                    if (to_resize.length < 1)
                    {
                        can_submit = true;
                        document.getElementById('f_submit').style.display = 'inline';
                        return;
                    }

                    var current = to_resize[0];
                    resize(
                        current[0], // file
                        config.thumb_width, // size
                        current[1], // image resized
                        current[2], // progress element
                        function () {
                            current[2].parentNode.removeChild(current[2]);
                            resizeFromList();
                        }
                    );

                    to_resize.splice(0, 1);
                }

                resizeFromList();
            };

            document.getElementById("f_upload").onsubmit = function ()
            {
                if (!can_submit)
                {
                    alert('A file is loading, please wait...');
                    return false;
                }

                if (document.getElementById('f_title').value.replace('/[\s]/g', '') == '')
                {
                    alert('Title is mandatory.');
                    return false;
                }

                if (document.getElementById("f_files").files.length < 1)
                {
                    alert('No file is selected.');
                    return false;
                }

                var l = parent.childNodes.length;

                if (l < 1)
                {
                    return false;
                }

                can_submit = false;
                document.getElementById('f_submit').style.display = 'none';

                var xhr = new XMLHttpRequest;

                var params = "album_create=1&title=" + encodeURIComponent(document.getElementById('f_title').value);
                params += "&private=" + (document.getElementById('f_private').checked ? '1' : '0');

                xhr.open('POST', config.base_url + '?album', true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.setRequestHeader("Content-length", params.length);
                xhr.setRequestHeader("Connection", "close");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 400)
                    {
                        alert(xhr.responseText);
                    }
                    else if (xhr.readyState == 4 && xhr.status == 200)
                    {
                        var txt = xhr.responseText.split('/');
                        album_id = txt[0];
                        album_check = txt[1];
                        uploadPicture(0, 0);
                    }
                };

                xhr.send(params);

                return false;
            };
        }
        else // Single file mode
        {
            var parent = document.getElementById('resizeParent');

            var figure = document.createElement('figure');
            var thumb = document.createElement('div');

            var progress = document.createElement('figcaption');
            progress.innerHTML = '<?php echo __('Please select a picture') ?>...';

            figure.appendChild(progress);
            figure.appendChild(thumb);
            parent.appendChild(figure);

            document.getElementById("f_file").onchange = function ()
            {
                if (!this.files.length)
                {
                    return false;
                }

                progress.style.display = "block";
                thumb.innerHTML = '';
                document.getElementById('f_submit').style.display = 'none';
                can_submit = false;

                if (/^image\/jpe?g$/i.test(this.files[0].type))
                {
                    can_submit = false;

                    resize(
                        this.files[0],
                        config.thumb_width, // thumb size
                        thumb, // thumb resized
                        progress,
                        function () {
                            if (thumb.firstChild)
                            {
                                progress.style.display = "none";
                            }
                            can_submit = true;
                            document.getElementById('f_submit').style.display = 'inline';
                        }
                    );
                }
                else
                {
                    var r = new RegExp('\.(' + config.allowed_formats.join('|') + ')$', 'i');

                    if (/^image\//i.test(this.files[0].type) && r.test(this.files[0].name))
                    {
                        progress.innerHTML = "<?php echo __('Image is recognized.'); ?>";
                        can_submit = true;
                        document.getElementById('f_submit').style.display = 'inline';
                    }
                    else
                    {
                        progress.innerHTML = '<?php echo __('The chosen file is not an image'); ?>: ' + this.files[0].type;
                        document.getElementById('f_submit').style.display = 'none';
                        return false;
                    }
                }

                if (document.getElementById("f_name").value != last_filename)
                    return;

                last_filename = this.files[0].name;
                document.getElementById("f_name").value = cleanFileName(this.files[0].name);
            }

            document.getElementById("f_upload").onsubmit = function ()
            {
                if (can_submit == 2)
                {
                    return true;
                }

                if (!can_submit)
                {
                    alert('File is loading, please wait...');
                    return false;
                }

                var file = document.getElementById('f_file');

                if (!file.files.length)
                {
                    alert('You must choose a file before sending.');
                    return false;
                }

                var div_img = document.createElement('div');
                div_img.style.display = "none";

                parent.appendChild(div_img);

                can_submit = false;
                document.getElementById('f_submit').style.display = 'none';

                if (/^image\/jpe?g$/i.test(file.files[0].type))
                {
                    var progress = document.createElement('span');
                    parent.firstChild.appendChild(progress);

                    resize(
                        file.files[0],
                        config.max_width, // thumb size
                        div_img, // thumb resized
                        progress,
                        function () {
                            progress.innerHTML = "<?php echo __('Uploading'); ?>... <img class=\"loading\" src=\"" + loading_gif + "\" alt=\"\" />";

                            var img = div_img.firstChild;

                            var name = document.createElement('input');
                            name.type = 'hidden';
                            name.name = file.name + '[name]';
                            name.value = file.value.replace(/^.*[\/\\]([^\/\\]*)$/, '$1');
                            file.parentNode.appendChild(name);

                            file.type = "hidden";
                            file.name = file.name + "[content]";
                            file.value = img.src.substr(img.src.indexOf(',') + 1);

                            can_submit = 2;
                            document.getElementById('f_upload').submit();
                        }
                    );

                    return false;
                }
                else
                {
                    var progress = document.createElement('p');
                    progress.innerHTML = "<?php echo __('Uploading'); ?>... <img class=\"loading\" src=\"" + loading_gif + "\" alt=\"\" />";
                    parent.firstChild.appendChild(progress);
                    return true;
                }
            };
        }
    };

    var canvas = document.createElement("canvas");

    function resize($file, $size, $img, $progress, $onload)
    {
        this._url = null;

        function resampled(data)
        {
            var img = ($img.lastChild || $img.appendChild(new Image));
            img.src = data;
            img.className = "preview";

            if (this._url && (window.URL || window.webkitURL).revokeObjectURL)
            {
                (window.URL || window.webkitURL).revokeObjectURL(this._url);
                this._url = null;
            }

            if ($onload)
            {
                $onload();
            }
            else
            {
                can_submit = true;
            }
        }

        function load(e) {
            Resample(
                this.result,
                this._resize,
                null,
                resampled
            );
        }

        function abort(e) {
            can_submit = true;
        }

        function error(e) {
            can_submit = true;
        }

        var size = parseInt($size, 10);

        if ((/^image\/jpe?g/.test($file.type)))
        {
            if ($progress)
            {
                $progress.innerHTML = "<?php echo __('Resizing'); ?>... <img class=\"loading\" src=\"" + loading_gif + "\" alt=\"\" />";
            }

            can_submit = false;

            if (!(window.URL || window.webkitURL) && FileReader)
            {
                var file = new FileReader;
                file.onload = load;
                file.onabort = abort;
                file.onerror = error;
                file._resize = size;
                file.readAsDataURL($file);
            }
            else
            {
                var url = (window.URL || window.webkitURL).createObjectURL($file);
                this._url = url;
                Resample(url, size, null, resampled);
            }
        }
        else
        {
            return false;
        }
    }

    var Resample = (function (canvas)
    {
        function Resample(img, width, height, onresample)
        {
            var load = typeof img == "string",
                i = load || img;

            if (load)
            {
                i = new Image;
                // with propers callbacks
                i.onload = onload;
                i.onerror = onerror;
            }

            i._onresample = onresample;
            i._width = width;
            i._height = height;
            load ? (i.src = img) : onload.call(img);
        }

        function onerror()
        {
            throw ("not found: " + this.src);
        }

        function onload()
        {
            var img = this,
                width = img._width,
                height = img._height,
                onresample = img._onresample
            ;

            if (height == null && width < 0)
            {
                var max_mp = Math.abs(width) * Math.abs(width);
                var img_mp = img.width * img.height;

                if (img_mp > max_mp)
                {
                    var ratio = img_mp / max_mp;
                    height = round(img.height / ratio);
                    width = round(img.width / ratio);
                }
                else
                {
                    width = img.width;
                    height = img.height;
                }
            }
            else if (height == null)
            {
                if (img.width > img.height)
                {
                    height = round(img.height * width / img.width)
                }
                else if (img.width == img.height)
                {
                    height = width;
                }
                else
                {
                    height = width;
                    width = round(img.width * height / img.height);
                }

                if (img.width < width && img.height < height)
                {
                    width = img.width, height = img.height;
                }
            }

            width = Math.abs(width);
            height = Math.abs(height);

            delete img._onresample;
            delete img._width;
            delete img._height;

            canvas.width = width;
            canvas.height = height;

            context.drawImage(
                img, // original image
                0, // starting x point
                0, // starting y point
                img.width, // image width
                img.height, // image height
                0, // destination x point
                0, // destination y point
                width, // destination width
                height // destination height
            );

            onresample(canvas.toDataURL("image/jpeg", 0.75));
            context.clearRect(0, 0, canvas.width, canvas.height);
        }

        var context = canvas.getContext("2d"),
            round = Math.round;

        return Resample;
    }
    (
        canvas
    ));
} ());
<?php exit; endif; ?><?php if (isset($_GET["css"])): header("Content-Type: text/css"); ?>
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed,
figure, figcaption, footer, header, hgroup,
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure,
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	line-height: 1;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}

h1 { font-size: 200%; }
h2 { font-size: 150%; }
h3 { font-size: 125%; }
h4 { font-size: 112.5%; }
h6 { font-size: 80%; }

body {
	background: #c4c0aa url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG0AAAB%2BBAMAAADclIJQAAAAAXNSR0IArs4c6QAAAA9QTFRFoKh%2Br7KQs7aTu7qexsCrr1fZwwAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxMAAAsTAQCanBgAAAAHdElNRQfcCR0WLiQp5Kn4AAADu0lEQVRYw%2B1XUXbjIAwUxgeADQdwGh%2FADjqAbXT%2FM%2B0HCATBebvddtt9Gz6atvFgIc1IDAAAKAMAbgMAACADeY071Mu%2B3bz3HjdY%2BF8qyI%2B4cILuGjYoO%2BIDzgU4WQuUr9zU4AZaznAGMB%2FHLQAA6siB4w7nyy31bwPjNIUnMNBHH3d5DgOgbpyO1uewkuuECzFImRJ1u5sH3JjqDbPIJ0rYjYjoXnaJkeREpMwGANDybDPdrb1RzgMDqKo7YJXkHPJAXJUh1Aek8loUZ%2BOTZhZwMONS4cYFQHJtb1mn04M6fRPyn4UzoIQ8cEslqoSQX0IlVUVd8UUGAIB4K6rz4zbBbalGcMEAuIMpnT6pbEsnwkXySBNWAipJdCFzCGslKSRa8wtSQmdTtt3rUgmkSERiWin2QKlW49YhdD693tqHFJI8eaO8vf5FBqV8P8wqLt7gYfdumOUxxrntRJrq5q8lzBxEOl%2FFKZkATUSla5TS5HDc0UuApnC1ud0IabIgQFEFnGPjiMVUeLQ91U2FucG0HQvpmoq6ZjTUBAPQRHdbdUhNlFqbpklh6NSfGUjBe%2B99TACFt1vKlwsow2mqpG5IcU28KWu%2BgvXIZK21VjQn6jypz%2Fq5LtqbpwecOh9W1FZK4vA4nQDz1DBKJB6COcWVhKWQM%2B6ywTlMyCjhKA%2BM5fmoMj2cwnrGqScKlefDIG8ZA8pZFa80uRLppGgAQFcZuRB5FKweN8lAWYeqNWpaAUDQNr6BD5gCnqeWJrg2V4%2FYqPmASWnj0rA5X4wye6L0uYMkBuit5Zpp2RppxAdMrXwINWecmB4p0tROqMIBGUHauo8NNAHAGIKsPOPcJnH1sHK02gutoU1k3NTg6S1zJqI9jWoeOfzEfIjp0wr3h7%2FCsFcjp3RK2nsjvBV7ggtFxbbzMMIb7aVwRcvIjbs%2FrGKv7l26dXphN0xo7igydaoRpzKPt1nGPU5CDlN5EjJisTBO0VkCkLwnVjJfwcrB3N6dqQqD4Z8AKmtz2M%2FabNxw5vOH8lMKiZVd%2FMPO46sIV4yPcZGFM8KvLJE9zJ%2BBVqSpO3MuROH%2BZq219seWxGpgSLQRRGpGI6gb8dpK5bhEAvbycV%2Fu4%2BZv5%2BP0p%2Fm46eXj%2FsTHDf%2BMj1MvH%2FfrPs68w8fZd%2Fm49cTHzV%2Ft46jycdcP9nGde5aqfZz6PR93tAR4%2BbiXj0slZh93fJaPm7%2BNj1v%2BCx%2B3fZaP08cTrX9DH4fv8nEDkvc5Vx%2Fp49a%2F7%2BPsu32c%2F1Mf9xMJCg1tRkA9NQAAAABJRU5ErkJggg%3D%3D');
	color: #000;
	padding: 1em;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
}

a { color: darkblue; }
a:hover { color: darkred; }

body > header, #page, body > footer {
	width: 90%;
	margin: 1em auto;
	border-radius: 1em;
	background: rgba(213, 214, 182, 0.75);
	padding: 1em;
	text-align: center;
}

body > header h1 a {
	text-decoration: none;
	color: #000;
	font-style: italic;
}

body > header h2 {
	color: darkred;
}

body > footer {
	font-size: .9em;
	color: #666;
}

nav {
	margin: .5em 0;
}

nav ul li {
	display: inline-block;
	margin: .2em .5em;
}

nav ul li a {
	color: #000;
	border-radius: .5em;
	background: rgba(255, 255, 255, 0.5);
	padding: .2em .4em;
}

nav ul li a:hover {
	background: #fff;
	color: darkred;
}

input[type=submit] {
	font-size: 112.5%;
	padding: .3em;
	cursor: pointer;
}

label {
	cursor: pointer;
}
label:hover {
	border-bottom: 1px dashed #fff;
}

input[type=text], input[type=file], input[type=password] {
	padding: .3em;
	width: 95%;
}

fieldset dl dt {
	font-weight: bold;
}

fieldset dl dd {
	margin: .5em;
}

fieldset {
	width: 50%;
	margin: 0 auto;
}

.info {
	margin: .8em 0;
	color: #666;
}

.picture footer {
	margin: 1em 0;
}

.picture footer.context {
	background: rgb(220, 220, 220);
	background: rgba(255, 255, 255, 0.25);
	border-radius: .5em;
	padding: 1em;
	max-width: 650px;
	margin: 1em auto;
}

.picture footer.context img {
	max-width: 200px;
	max-height: 150px;
}

.picture footer.context figure {
	position: relative;
	width: 200px;
	height: 180px;
	margin: 0;
}

.picture footer.context figure b {
	font-size: 100px;
	line-height: 150px;
	width: 200px;
	height: 150px;
	position: absolute;
	display: block;
	top: 0;
	left: 0;
	color: rgb(255, 255, 255);
	color: rgba(255, 255, 255, 0.5);
	text-shadow: 0px 0px 10px #000;
}

.examples dt {
	margin: .5em 0;
	font-weight: bold;
}

.examples input, .admin input[type=text], .examples textarea {
	text-align: center;
	background: rgba(213, 214, 182, 0.5);
	border: 1px solid #fff;
	border-radius: .5em;
	font-family: "Courier New", Courier, monospace;
	max-width: 50em;
	width: 100%;
	font-size: 10pt;
	padding: .2em;
}

figure {
	display: inline-block;
	margin: 1em;
	vertical-align: middle;
	position: relative;
	min-width: 150px;
}

figure figcaption {
	font-size: small;
	margin-top: .5em;
}

figure a:hover img {
	box-shadow: 0px 0px 10px #000;
	background: #fff;
}

figure span {
	background: rgb(0, 0, 0);
	background: rgba(0, 0, 0, 0.75);
	color: #fff;
	position: absolute;
	padding: .5em 1em;
	font-weight: bold;
	top: 1em;
	left: 0;
}

figure span.private {
	background: rgb(150, 0, 0);
	background: rgba(150, 0, 0, 0.75);
}

.pagination .selected {
	font-weight: bold;
	font-size: 125%;
}

#resizeParent, p.admin, #albumParent {
	background: rgb(100, 100, 100);
	background: rgba(0, 0, 0, 0.25);
	padding: 1em;
	width: 50%;
	margin: .8em auto;
	border-radius: .8em;
	color: #fff;
}

#albumParent {
	width: 90%;
}

#resizeParent img.loading, #albumParent img.loading, #albumParent span b {
	background: #fff;
	padding: .5em;
	border-radius: 1em;
	vertical-align: middle;
	color: #000;
	display: inline-block;
	line-height: 16px;
}

#resizeParent img, #albumParent img {
	box-shadow: 0px 0px 10px #000;
}

article h2 {
	margin-bottom: .5em;
}

p.error {
	color: red;
	margin: .8em;
}

p.admin a {
	color: darkred;
}

.albums figure {
	display: block;
}

.albums figure img {
	vertical-align: middle;
	margin: .5em;
}

.albums figure span {
	margin: 0 auto;
	left: 0px;
	right: 0px;
	bottom: 40%;
	top: auto;
}<?php exit; endif; ?><?php

class Fotoo_Hosting
{
	static private $base_index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	private $db = null;
	private $config = null;

	public function __construct(&$config)
	{
		$init = file_exists($config->db_file) ? false : true;
		$this->db = new SQLite3($config->db_file);

		if (!$this->db)
		{
			throw new FotooException("SQLite database init error.");
		}

		if ($init)
		{
			$this->db->exec('
				CREATE TABLE pictures (
					hash TEXT PRIMARY KEY NOT NULL,
					filename TEXT NOT NULL,
					date INT NOT NULL,
					format TEXT NOT NULL,
					width INT NOT NULL,
					height INT NOT NULL,
					thumb INT NOT NULL DEFAULT 0,
					private INT NOT NULL DEFAULT 0,
					size INT NOT NULL DEFAULT 0,
					album TEXT NULL,
					ip TEXT NULL,
					punid INT NOT NULL,
					punname TEXT NULL,
					import INT NULL
				);

				CREATE INDEX date ON pictures (private, date);
				CREATE INDEX album ON pictures (album);

				CREATE TABLE stats (
					punid INT PRIMARY KEY NOT NULL,
					punname TEXT NULL,
					punstorage INT NOT NULL,
					nbimage INT NOT NULL
				);

				CREATE TABLE albums (
					hash TEXT PRIMARY KEY NOT NULL,
					title TEXT NOT NULL,
					date INT NOT NULL,
					private INT NOT NULL DEFAULT 0,
					punid INT NOT NULL,
					punname TEXT NULL
				);
			');
		}

		$this->config =& $config;

		if (!file_exists($config->storage_path))
			mkdir($config->storage_path);
	}


	public function isPrivateAllowed()
	{
		if ( in_array($GLOBALS['punusergroup'], $this->config->allow_private ) )
			return true;
	}

    public function isClientBanned()
    {
    	if (!empty($_COOKIE['bstats']))
    		return true;

    	if (count($this->config->banned_ips) < 1)
    		return false;

        if (!empty($_SERVER['REMOTE_ADDR']) && self::isIpBanned($_SERVER['REMOTE_ADDR'], $this->config->banned_ips))
        {
        	return true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)
        	&& self::isIpBanned($_SERVER['HTTP_X_FORWARDED_FOR'], $this->config->banned_ips))
        {
        	return true;
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)
        	&& self::isIpBanned($_SERVER['HTTP_CLIENT_IP'], $this->config->banned_ips))
        {
        	return true;
        }

        return false;
    }

    public function setBanCookie()
    {
    	return setcookie('bstats', md5(time()), time()+10*365*24*3600, '/');
    }

    static public function getIPAsString()
    {
    	$out = '';

        if (!empty($_SERVER['REMOTE_ADDR']))
        {
            $out .= $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
        {
        	$out .= (!empty($out) ? ', ' : '') . 'X-Forwarded-For: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
        {
        	$out .= (!empty($out) ? ', ' : '') . 'Client-IP: ' . $_SERVER['HTTP_CLIENT_IP'];
        }

        return $out;
    }

    /**
     * Returns an integer if $ip is in addresses given in $check array
     * This integer may be used to store the IP address in database eventually
     *
     * Examples:
     * - check_ip('192.168.1.102', array('192.168.1.*'))
     * - check_ip('2a01:e34:ee89:c060:503f:d19b:b8fa:32fd', array('2a01::*'))
     * - check_ip('2a01:e34:ee89:c060:503f:d19b:b8fa:32fd', array('2a01:e34:ee89:c06::/64'))
     */
    static public function isIpBanned($ip = null, $check)
    {
        $ip = strtolower(is_null($ip) ? $_SERVER['REMOTE_ADDR'] : $ip);

        if (strpos($ip, ':') === false)
        {
            $ipv6 = false;
            $ip = ip2long($ip);
        }
        else
        {
            $ipv6 = true;
            $ip = bin2hex(inet_pton($ip));
        }

        foreach ($check as $c)
        {
            if (strpos($c, ':') === false)
            {
                if ($ipv6)
                {
                    continue;
                }

                // Check against mask
                if (strpos($c, '/') !== false)
                {
                    list($c, $mask) = explode('/', $c);
                    $c = ip2long($c);
                    $mask = ~((1 << (32 - $mask)) - 1);

                    if (($ip & $mask) == $c)
                    {
                        return $c;
                    }
                }
                elseif (strpos($c, '*') !== false)
                {
                    $c = substr($c, 0, -1);
                    $mask = substr_count($c, '.');
                    $c .= '0' . str_repeat('.0', (3 - $mask));
                    $c = ip2long($c);
                    $mask = ~((1 << (32 - ($mask * 8))) - 1);

                    if (($ip & $mask) == $c)
                    {
                        return $c;
                    }
                }
                else
                {
                    if ($ip == ip2long($c))
                    {
                        return $c;
                    }
                }
            }
            else
            {
                if (!$ipv6)
                {
                    continue;
                }

                // Check against mask
                if (strpos($c, '/') !== false)
                {
                    list($c, $mask) = explode('/', $c);
                    $c = bin2hex(inet_pton($c));
                    $mask = $mask / 4;
                    $c = substr($c, 0, $mask);

                    if (substr($ip, 0, $mask) == $c)
                    {
                        return $c;
                    }
                }
                elseif (strpos($c, '*') !== false)
                {
                    $c = substr($c, 0, -1);
                    $c = bin2hex(inet_pton($c));
                    $c = rtrim($c, '0');

                    if (substr($ip, 0, strlen($c)) == $c)
                    {
                        return $c;
                    }
                }
                else
                {
                    if ($ip == inet_pton($c))
                    {
                        return $c;
                    }
                }
            }
        }

        return false;
    }

	static private function baseConv($num, $base=null)
	{
		if (is_null($base))
			$base = strlen(self::$base_index);

		$index = substr(self::$base_index, 0, $base);

		$out = "";
		for ($t = floor(log10($num) / log10($base)); $t >= 0; $t--)
		{
			$a = floor($num / pow($base, $t));
			$out = $out . substr($index, $a, 1);
			$num = $num - ($a * pow($base, $t));
		}

		return $out;
	}

	static public function getErrorMessage($error)
	{
		switch ($error)
		{
			case UPLOAD_ERR_INI_SIZE:
				return __('The uploaded file exceeds the allowed file size (ini).');
			case UPLOAD_ERR_FORM_SIZE:
				return __('The uploaded file exceeds the allowed file size (html).');
			case UPLOAD_ERR_PARTIAL:
				return __('The uploaded file was only partially uploaded.');
			case UPLOAD_ERR_NO_FILE:
				return __('No file was uploaded.');
			case UPLOAD_ERR_NO_TMP_DIR:
				return __('Missing a temporary folder.');
			case UPLOAD_ERR_CANT_WRITE:
				return __('Failed to write file to disk.');
			case UPLOAD_ERR_EXTENSION:
				return __('A server extension stopped the file upload.');
			case UPLOAD_ERR_INVALID_IMAGE:
				return __('Invalid image format.');
			default:
				return __('Unknown error.');
		}
	}

	static public function getInfoMessage ()
    {
		$info = '';
		if ( !isset($_GET['list']) && !isset($_GET['albums']) )
		{
			$info = '
			<div id="announce-block" class="inbox">
				<div class="usercontent">
					<div class="info">
						Pour assurer sa pérennité utilisez ce service avec discernement :
							<ul>
								<li>Sélectionnez bien vos images (l\'espace de stockage commun disponible n\'est pas illimité)&nbsp;;
								<li>Les images hébergées sur randonner-leger.org grâce à ce service n\'ont pas vocation à être affichées sur un autre site&nbsp;;
								<li>Tout fichier ne respectant pas le code de la propriété intellectuelle ou la législation en vigueur pourra être supprimé sans préavis et le compte utilisateur fermé.
							</ul>
							En envoyant vos images vous acceptez qu\'elles soient publiées sous la license <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/fr/" target="_blank">Creative Commons BY-NC-SA</a>.
					</div>
				</div>
			</div>';
		}
		return $info;

    }

	protected function _processEncodedUpload(&$file)
	{
		if (!is_array($file))
		{
			return false;
		}

		$file['error'] = $file['size'] = 0;

		if (empty($file['content']))
		{
			$file['error'] = UPLOAD_ERR_NO_FILE;
			return false;
		}

		if (!is_string($file['content']))
		{
			$file['error'] = UPLOAD_ERR_NO_FILE;
			return false;
		}

		$file['content'] = base64_decode($file['content'], true);

		if (empty($file['content']))
		{
			$file['error'] = UPLOAD_ERR_PARTIAL;
			return false;
		}

		$file['size'] = strlen($file['content']);

		if ($file['size'] == 0)
		{
			$file['error'] = UPLOAD_ERR_FORM_SIZE;
			return false;
		}

		$file['tmp_name'] = tempnam(ini_get('upload_tmp_dir') ?: sys_get_temp_dir(), 'tmp_file_');

		if (!$file['tmp_name'])
		{
			$file['error'] = UPLOAD_ERR_NO_TMP_DIR;
			return false;
		}

		if (!file_put_contents($file['tmp_name'], $file['content']))
		{
			$file['error'] = UPLOAD_ERR_CANT_WRITE;
			return false;
		}

		unset($file['content']);

		return true;
	}

	public function upload($file, $name = '', $private = false, $album = null)
	{
		if ($this->isClientBanned())
		{
			throw new FotooException('Upload error: upload not permitted.', -42);
		}

		$client_resize = false;

		if (isset($file['content']) && $this->_processEncodedUpload($file))
		{
			$client_resize = true;
		}

		if (!isset($file['error']))
		{
			return false;
		}

		if ($file['error'] != UPLOAD_ERR_OK)
		{
			throw new FotooException("Upload error.", $file['error']);
		}

		if (!empty($name))
		{
			$name = preg_replace('!\s+!', '-', $name);
			$name = preg_replace('![^a-z0-9_.-]!i', '', $name);
			$name = preg_replace('!([_.-]){2,}!', '\\1', $name);
			$name = substr($name, 0, 30);
		}

		if (!trim($name))
		{
			$name = '';
		}

		$options = array();
		$options[image::USE_GD_FAST_RESIZE_TRICK] = true;

		$img = image::identify($file['tmp_name'], $options);

		if (empty($img) || empty($img['format']) || empty($img['width']) || empty($img['height'])
			|| !in_array($img['format'], $this->config->allowed_formats))
		{
			@unlink($file['tmp_name']);
			throw new FotooException("Invalid image format.", UPLOAD_ERR_INVALID_IMAGE);
		}

		if ($img['format'] != 'PNG' && $img['format'] != 'JPEG' && $img['format'] != 'GIF')
		{
			$options[image::FORCE_IMAGICK] = true;
		}

		$size = filesize($file['tmp_name']);

		$hash = md5($file['tmp_name'] . time() . $img['width'] . $img['height'] . $img['format'] . $size . $file['name']);
		$dest = $this->config->storage_path . substr($hash, -2);

		if (!file_exists($dest))
			mkdir($dest);

		$base = self::baseConv(hexdec(uniqid()));
		$dest .= '/' . $base;
		$ext = '.' . strtolower($img['format']);

		if (trim($name) && !empty($name))
			$dest .= '.' . $name;

		$max_mp = $this->config->max_width * $this->config->max_width;
		$img_mp = $img['width'] * $img['height'];

		if ($img_mp > $max_mp)
		{
			$ratio = $img_mp / $max_mp;
			// $width = round($img['width'] / $ratio);
			$width = $this->config->max_width;
			// $height = round($img['height'] / $ratio);
			$height = $this->config->max_width;
			$resize = true;
		}
		else
		{
			$width = $img['width'];
			$height = $img['height'];
			$resize = false;
		}

		// If JPEG or big PNG/GIF, then resize (always resize JPEG to reduce file size)
		if ($resize || ($img['format'] == 'JPEG' && !$client_resize)
			|| (($img['format'] == 'GIF' || $img['format'] == 'PNG') && $file['size'] > (1024 * 1024)))
		{
			$res = image::resize(
				$file['tmp_name'],
				$dest . $ext,
				$width,
				$height,
				array(
					image::JPEG_QUALITY => 80,
					image::USE_GD_FAST_RESIZE_TRICK => true
				)
			);

			if (!$res)
			{
				return false;
			}
			else
			{
				list($width, $height) = $res;
			}
		}
		elseif ($client_resize)
		{
			rename($file['tmp_name'], $dest . $ext);
		}
		else
		{
			move_uploaded_file($file['tmp_name'], $dest . $ext);
		}

		$size = filesize($dest . $ext);
		chmod($dest . $ext, 0644);

		// Create thumb when needed
		if ($width > $this->config->thumb_width || $height > $this->config->thumb_width
			|| $size > (100 * 1024) || ($img['format'] != 'JPEG' && $img['format'] != 'PNG'))
		{
			$options[image::JPEG_QUALITY] = 70;
			$thumb_ext = '.s.' . strtolower($img['format']);

			if ($img['format'] != 'JPEG' && $img['format'] != 'PNG')
			{
				$options[image::FORCE_OUTPUT_FORMAT] = 'JPEG';
				$thumb_ext = '.s.jpeg';
			}

			image::resize(
				$dest . $ext,
				$dest . $thumb_ext,
				($width > $this->config->thumb_width) ? $this->config->thumb_width : $width,
				($height > $this->config->thumb_width) ? $this->config->thumb_width : $height,
				$options
			);

			$thumb = true;
		}
		else
		{
			$thumb = false;
		}

		$hash = substr($hash, -2) . '/' . $base;

		$req = $this->db->prepare('INSERT INTO pictures
			(hash, filename, date, format, width, height, thumb, private, size, album, ip, punid, punname, import )
			VALUES (:hash, :filename, :date, :format, :width, :height, :thumb, :private, :size, :album, :ip, :punid, :punname, :import );');

		$req->bindValue(':hash', $hash);
		$req->bindValue(':filename', $name);
		$req->bindValue(':date', time());
		$req->bindValue(':format', $img['format']);
		$req->bindValue(':width', (int)$width);
		$req->bindValue(':height', (int)$height);
		$req->bindValue(':thumb', (int)$thumb);
		$req->bindValue(':private', $private && $this->isPrivateAllowed() ? '1' : '0');
		$req->bindValue(':size', (int)$size);
		$req->bindValue(':album', is_null($album) ? NULL : $album);
		$req->bindValue(':ip', self::getIPAsString());
		$req->bindValue(':punid', (int)$GLOBALS['punid']);
		$req->bindValue(':punname', $GLOBALS['punname']);
		$req->bindValue(':import', '0');

		$req->execute();

		// Automated deletion of IP addresses to comply with local low
		$expiration = time() - ($this->config->ip_storage_expiration * 24 * 3600);
		$this->db->query('UPDATE pictures SET ip = "R" WHERE date < ' . (int)$expiration . ';');

		// Stats
		$punstats = $this->db->querySingle('SELECT * FROM stats WHERE punid == ' . $GLOBALS['punid'] .'', true);

		if (empty($punstats)) {
			$punstats['punstorage'] = 0;
			$punstats['nbimage'] = 0;
		}

		$req = $this->db->prepare('INSERT OR REPLACE INTO stats
		 	(punid, punname, punstorage, nbimage )
		 	VALUES (:punid, :punname, :punstorage, :nbimage );');

		$req->bindValue(':punid', $GLOBALS['punid']);
		$req->bindValue(':punname', $GLOBALS['punname']);
		$req->bindValue(':punstorage', (int)$punstats['punstorage'] + (int)$size);
		$req->bindValue(':nbimage', (int)$punstats['nbimage'] + 1 );
		$req->execute();

		return $hash;
	}

	public function get($hash)
	{
		$res = $this->db->querySingle(
			'SELECT * FROM pictures WHERE hash = \''.$this->db->escapeString($hash).'\';',
			true
		);

		if (empty($res))
			return false;

		$file = $this->_getPath($res);
		$th = $this->_getPath($res, 's');

		if (!file_exists($file))
		{
			if (file_exists($th))
				@unlink($th);

			$this->db->exec('DELETE FROM pictures WHERE hash = \''.$res['hash'].'\';');
			return false;
		}

		return $res;
	}

	public function remove($hash, $id = null)
	{
		if (!$this->logged() && !$this->checkRemoveId($hash, $id))
			return false;

		$img = $this->get($hash);

		$file = $this->_getPath($img);

		if (file_exists($file)) {

		$res = $this->db->querySingle(
			'SELECT * FROM pictures WHERE hash = \''.$hash.'\';',
			true
		);

		$this->db->exec('UPDATE stats SET punstorage = punstorage - "' . $res['size'] . '", nbimage = nbimage - 1 WHERE punid = \'' . $res['punid'] .'\';');

			unlink($file);
		}

		return $this->get($hash) ? false : true;
	}

	public function rotate($hash, $id = null)
	{

		if (!$this->logged() && !$this->checkRemoveId($hash, $id))
			return false;

		$img = $this->get($hash);
		$file = $this->_getPath($img);

		if (!file_exists($file))
			return false;

		return $this->rotation($hash) ? false : true;
	}

    public function rotation($hash, $angle = 90, $id = null, $savename = false)
    {

		$res = $this->db->querySingle(
			'SELECT * FROM pictures WHERE hash = \''.$this->db->escapeString($hash).'\';',
			true
		);

        if ($res['format'] != 'JPEG')
            return false;

		$file = $this->_getPath($res);
		$th = $this->_getPath($res, 's');
        $angle= filter_var($_GET['angle'], FILTER_SANITIZE_NUMBER_INT);

        if ( $angle == 90 || $angle == 180 || $angle == 270 ) {

            $original   = imagecreatefromjpeg($file);
            $rotated    = imagerotate($original, $angle, 0);
            $savename   = $file;

            $originalth = imagecreatefromjpeg($th);
            $rotatedth  = imagerotate($originalth, $angle, 0);
            $savenameth = $th;

            // Save the original file
            imagejpeg($rotated,$savename,70);
            imagedestroy($rotated);

            // Save the thumb
            imagejpeg($rotatedth,$savenameth,70);
            imagedestroy($rotatedth);

			if( $angle != 180  )
				$this->db->exec('UPDATE pictures SET width = "' . $res['height'] . '", height = ' . $res['width'] . ' WHERE hash = \'' . $res['hash'] .'\';');

        } else {

            return false;

        }

        }

	public function getList($page)
	{
		$begin = ($page - 1) * $this->config->nb_pictures_by_page;
		if ( isset($_GET['mesphotos']) ) {
			$where = 'WHERE punid == ' . $GLOBALS['punid'] .'';
		} else {
			$where = $this->logged() ? '' : 'WHERE private != 1 AND import != 1';
		}

		$out = array();
		$res = $this->db->query('SELECT * FROM pictures '.$where.' ORDER BY date DESC LIMIT '.$begin.','.$this->config->nb_pictures_by_page.';');

		while ($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			$out[] = $row;
		}

		return $out;
	}

	public function getStats()
	{
		$out = array();
		if ( isset($_GET['stats']) && $_GET['stats'] == 'img' ) {
			$orderby = 'nbimage';
		} else {
			$orderby = 'punstorage';
		}
		$res = $this->db->query('SELECT * FROM stats ORDER BY ' . $orderby . ' DESC LIMIT 1000;');

		while ($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			$out[] = $row;
		}

		return $out;
	}


	public function makeRemoveId($hash)
	{
		return sha1($this->config->storage_path . $hash);
	}

	public function checkRemoveId($hash, $id)
	{
		return sha1($this->config->storage_path . $hash) === $id;
	}

	public function countList()
	{
		if ( isset($_GET['mesphotos']) ) {
			$where = 'WHERE punid == ' . $GLOBALS['punid'] .'';
		} else {
			$where = $this->logged() ? '' : 'WHERE private != 1 AND import != 1';
		}
		return $this->db->querySingle('SELECT COUNT(*) FROM pictures '.$where.';');
	}

	public function getAlbumList($page)
	{
		$begin = ($page - 1) * round($this->config->nb_pictures_by_page / 2);
#		$where = $this->logged() ? '' : 'WHERE private != 1';
		if ( isset($_GET['mesalbums']) ) {
			$where = 'WHERE punid == ' . $GLOBALS['punid'] .'';
		} else {
			$where = $this->logged() ? '' : 'WHERE private != 1';
		}

		$out = array();
		$res = $this->db->query('SELECT * FROM albums '.$where.' ORDER BY date DESC LIMIT '.$begin.','.round($this->config->nb_pictures_by_page / 2).';');

		while ($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			$row['extract'] = $this->getAlbumExtract($row['hash']);
			$out[] = $row;
		}

		return $out;
	}

	public function getAlbumPrevNext($album, $current, $order = -1)
	{
		$st = $this->db->prepare('SELECT * FROM pictures WHERE album = :album
			AND rowid '.($order > 0 ? '>' : '<').' (SELECT rowid FROM pictures WHERE hash = :img)
			ORDER BY rowid '.($order > 0 ? 'ASC': 'DESC').' LIMIT 1;');
		$st->bindValue(':album', $album);
		$st->bindValue(':img', $current);
		$res = $st->execute();

		if ($res)
			return $res->fetchArray(SQLITE3_ASSOC);

		return false;
	}

	public function getAlbumExtract($hash)
	{
		$out = array();
		$res = $this->db->query('SELECT * FROM pictures WHERE album = \''.$this->db->escapeString($hash).'\' ORDER BY RANDOM() LIMIT 2;');

		while ($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			$out[] = $row;
		}

		return $out;
	}

	public function countAlbumList()
	{
		if ( isset($_GET['mesalbums']) ) {
			$where = 'WHERE punid == ' . $GLOBALS['punid'] .'';
		} else {
			$where = $this->logged() ? '' : 'WHERE private != 1';
		}
		return $this->db->querySingle('SELECT COUNT(*) FROM albums '.$where.';');
	}

	public function getAlbum($hash)
	{
		return $this->db->querySingle('SELECT *, strftime(\'%s\', date) AS date FROM albums WHERE hash = \''.$this->db->escapeString($hash).'\';', true);
	}

	public function getAlbumPictures($hash, $page)
	{
		$begin = ($page - 1) * $this->config->nb_pictures_by_page;

		$out = array();
		$res = $this->db->query('SELECT * FROM pictures WHERE album = \''.$this->db->escapeString($hash).'\' ORDER BY date LIMIT '.$begin.','.$this->config->nb_pictures_by_page.';');

		while ($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			$out[] = $row;
		}

		return $out;
	}

	public function countAlbumPictures($hash)
	{
		return $this->db->querySingle('SELECT COUNT(*) FROM pictures WHERE album = \''.$this->db->escapeString($hash).'\';');
	}

	public function removeAlbum($hash, $id = null)
	{
		if (!$this->logged() && !$this->checkRemoveId($hash, $id))
			return false;

		$imageremoved=0;
		$sizeremoved=0;

		$res = $this->db->query('SELECT * FROM pictures WHERE album = \''.$this->db->escapeString($hash).'\';');
		while ($row = $res->fetchArray(SQLITE3_ASSOC))
		{
			$file = $this->_getPath($row);

			if (file_exists($file))
				$punid = $row['punid'];
				$imageremoved++;
				$sizeremoved=$sizeremoved + $row['size'];
				unlink($file);

			if ($this->get($row['hash']))
				return false;
		}

		$this->db->exec('UPDATE stats SET punstorage = punstorage - "' . $sizeremoved . '", nbimage = nbimage - ' . $imageremoved . ' WHERE punid = \'' . $punid .'\';');
		$this->db->exec('DELETE FROM albums WHERE hash = \''.$this->db->escapeString($hash).'\';');
		return true;
	}

	public function removeAlbumEmpty($hash, $id = null)
	{
		if (!$this->checkRemoveId($hash, $id))
			return false;

		$this->db->exec('DELETE FROM albums WHERE hash = \''.$this->db->escapeString($hash).'\';');
		return true;
	}

	public function isDeleteButtonDisplayed($date, $punid)
	{
		if ( $this->logged() || ( $GLOBALS['punid'] == $punid && ( time() - $date ) < 86400 ) )
			return true;
	}

	public function setcookieFotooModo()
	{
		if ( $this->logged() ) :
			setcookie('FotooModo', 1, time() + 3600 , "/");
		endif;
	}

	public function unsetcookieFotooModo()
	{
		unset($_COOKIE['FotooModo']);
		setcookie('FotooModo', null, -1, '/');
	}

	public function getPictureSize($size)
	{
	    if ($size > (1024 * 1024))
	        $size = round($size / 1024 / 1024, 2) . ' MB';
	    elseif ($size > 1024)
	        $size = round($size / 1024, 2) . ' KB';
	    else
	        $size = $size . ' B';

			return $size;
	}

	protected function _getPath($img, $optional = '')
	{
		return $this->config->storage_path . $img['hash']
			. ($img['filename'] ? '.' . $img['filename'] : '')
			. ($optional ? '.' . $optional : '')
			. '.' . strtolower($img['format']);
	}

	public function getUrl($img, $append_id = false)
	{
		$url = $this->config->image_page_url
			. $img['hash']
			. ($img['filename'] ? '.' . $img['filename'] : '')
			. '.' . strtolower($img['format']);

		if ($append_id)
		{
			$id = $this->makeRemoveId($img['hash']);
			$url .= (strpos($url, '?') !== false) ? '&c=' . $id : '?c=' . $id;
		}

		return $url;
	}

	public function getImageWidth($img, $append_id = false)
	{
		$width = $img['width'];

		return intval($width);
	}

	public function getImageHeight($img, $append_id = false)
	{
		$height = $img['height'];

		return intval($height);
	}

	public function getImageUrl($img)
	{
		$url = $this->config->storage_url . $img['hash'];
		$url.= !empty($img['filename']) ? '.' . $img['filename'] : '';
		$url.= '.' . strtolower($img['format']);
		return $url;
	}

	public function getImageAuthor($img)
	{
		$author = $img['punname'];
		return $author;
	}

	public function getImagePunID($img)
	{
		$author = $img['punid'];
		return $author;
	}

	public function getImageAuthorBouton($img)
	{
		$author = null != ( $this->getImageAuthor($img) ) ? $this->getImageAuthor($img)  : '' ;
		$size = null != $img['size'] ? $this->getPictureSize($img['size']) : '' ;
		$punid = (null != $this->getImagePunID($img) ) ? $this->getImagePunID($img) : '' ;
		$spacer = '';
		$url = '';

		if ( !isset($_GET['mesphotos']) ) {
			if ($this->logged() )
				$spacer = (null != $punid) ? ' (id: ' . $punid .')' : '' ;

			$url = '
			<p class="meta profile">
				<a href="../profile.php?id='. $punid .'" class="author">' . $author . $spacer . '</a><br />
				<i class="size">' . $this->getPictureSize($img['size']) . '<br />
				<time datetime="'.date(DATE_W3C, $img['date']).'">'.strftime('%a %e %b %Y', $img['date']).'</time></i>
			</p>';
		}
		return $url;
	}

	public function getAlbumAuthorBouton($author, $punid, $date)
	{
		$punid = ( null != $punid ) ? $punid : '' ;
		$spacer = '';
		$url ='';

		if ( !isset ( $_GET['mesalbums'] ) ) {
			if ($this->logged() )
				$spacer = (null != $punid ) ? ' (id: ' . $punid .')' : '' ;

			$url = '<p class="meta profile">
				<a href="../profile.php?id='. $punid .'" class="author">' . $author . $spacer . '</a><br />
				<i><time datetime="'.date(DATE_W3C, $date).'">'.strftime('%a %e %b %Y', $date).'</time></i>
			</p>';
		}
		return $url;
	}

	public function getImageThumbUrl($img)
	{
		if (!$img['thumb'])
		{
			return $this->getImageUrl($img);
		}

		if ($img['format'] != 'JPEG' && $img['format'] != 'PNG')
		{
			$format = 'jpeg';
		}
		else
		{
			$format = strtolower($img['format']);
		}

		$url = $this->config->storage_url . $img['hash'];
		$url.= !empty($img['filename']) ? '.' . $img['filename'] : '';
		$url.= '.s.' . $format;
		return $url;
	}

	public function getImageThumbUrlForSearch($img, $import = null)
	{
		if (!$img['thumb'])
		{
			return $this->getImageUrl($img);
		}

		if ($img['format'] != 'JPEG' && $img['format'] != 'PNG')
		{
			$format = 'jpeg';
		}
		else
		{
			$format = strtolower($img['format']);
		}

		$url = '';

		if ( $import != 1 )
			$url = $this->config->storage_url . $img['hash'] . '.';

		$url .= !empty($img['filename']) ? $img['filename'] : '';

		return $url;
	}

	public function getShortImageUrl($img)
	{
		return $this->config->image_page_url
			. 'r.' . $img['hash'];
	}

	public function login($password)
	{
		if ($this->config->admin_password === $password)
		{
			@session_start();
			$_SESSION['logged'] = true;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function logged()
	{
		if (array_key_exists(session_name(), $_COOKIE) && !isset($_SESSION))
		{
			session_start();
		}

		return empty($_SESSION['logged']) ? false : true;
	}

	public function logout()
	{
		$this->logged();
		$_SESSION = null;
		session_destroy();
		return true;
	}

	public function createAlbum($title, $private = false)
	{
		if ($this->isClientBanned())
		{
			throw new FotooException('Upload error: upload not permitted.');
		}

		$hash = self::baseConv(hexdec(uniqid()));
		$private = $this->isPrivateAllowed() ? (int)(bool)$private : 0 ;
		$this->db->exec('INSERT INTO albums (hash, title, date, private, punid, punname )
			VALUES (\''.$this->db->escapeString($hash).'\',
			\''.$this->db->escapeString(trim($title)).'\',
			datetime(\'now\'), \''.$private.'\',
			\''.$GLOBALS['punid'].'\', \''.$GLOBALS['punname'].'\');');
		return $hash;
	}

	public function appendToAlbum($album, $name, $file)
	{
		$album = $this->db->querySingle('SELECT * FROM albums WHERE hash = \''.$this->db->escapeString($album).'\';', true);

		if (!$album)
		{
			return false;
		}

		return $this->upload($file, $name, $album['private'], $album['hash']);
	}
}

function refresh(){
$refresh ='?'.time();
return $refresh;
}

?><?php

// Generic image resize library
// Copyleft (C) 2005-11 BohwaZ <http://dev.kd2.org/>
// Licensed under the GNU LGPL licence

class imageLibException extends Exception
{
}

class image
{
    // Image aspect options
    const CROP = 'CROP';
    const IGNORE_ASPECT_RATIO = 'IGNORE_ASPECT_RATIO';
    const FORCE_SIZE_USING_BACKGROUND_COLOR = 'FORCE_SIZE_USING_BACKGROUND_COLOR';

    // Libs options
    const IMAGE_LIB = 'IMAGE_LIB';
    const FORCE_GD = 'FORCE_GD';
    const FORCE_IMAGICK = 'FORCE_IMAGICK';
    const FORCE_IMLIB = 'FORCE_IMLIB';
    const USE_GD_FAST_RESIZE_TRICK = 'USE_GD_FAST_RESIZE_TRICK';
    const ENABLE_REPORT = 'ENABLE_REPORT';

    // Formats options
    const FORCE_OUTPUT_FORMAT = 'FORCE_OUTPUT_FORMAT';
    const PROGRESSIVE_JPEG = 'PROGRESSIVE_JPEG';
    const JPEG_QUALITY = 'JPEG_QUALITY';
    const PNG_COMPRESSION = 'PNG_COMPRESSION';

    // Libs
    const IMLIB = 1;
    const IMAGICK = 2;
    const GD = 3;

    const TRANSPARENT_COLOR = 'transparent';

    static public $default_jpeg_quality = 75;
    static public $default_png_compression = 9;
    static public $default_background_color = '000000';

    static private $report = array(
    );

    static protected $options = array(
    );

    static protected $cache = array(
    );

    static public function canUseImlib()
    {
        return (extension_loaded('imlib') && function_exists('imlib_load_image'));
    }

    static public function canUseImagick()
    {
        return (extension_loaded('imagick') && class_exists('Imagick'));
    }

    static public function canUseGD()
    {
        return (extension_loaded('gd') && function_exists('imagecreatefromjpeg'));
    }

    static protected function option($id)
    {
        if (array_key_exists($id, self::$options))
            return self::$options[$id];
        else
            return false;
    }

    static protected function parseOptions($options)
    {
        self::$options = array();

        foreach ($options as $key=>$value)
        {
            if (defined($key))
                self::$options[$key] = $value;
            elseif (defined($value))
                self::$options[$value] = true;
            elseif (defined('image::'.$value))
                self::$options[$value] = true;
            elseif (defined('image::'.$key))
                self::$options[constant('image::'.$key)] = $value;
        }

        unset($options);

        if (self::option(self::FORCE_IMLIB))
        {
            if (!self::canUseImlib())
                throw new imageLibException('Imlib seems not installed');

            self::$options[self::IMAGE_LIB] = self::IMLIB;
        }
        elseif (self::option(self::FORCE_GD))
        {
            if (!self::canUseGD())
                throw new imageLibException('GD seems not installed');

            self::$options[self::IMAGE_LIB] = self::GD;
        }
        elseif (self::option(self::FORCE_IMAGICK))
        {
            if (!self::canUseImagick())
                throw new imageLibException('Imagick seems not installed');

            self::$options[self::IMAGE_LIB] = self::IMAGICK;
        }

        if ($format = self::option(self::FORCE_OUTPUT_FORMAT))
        {
            if ($format != 'JPEG' && $format != 'PNG')
            {
                throw new imageLibException('FORCE_OUTPUT_FORMAT must be either JPEG or PNG');
            }
        }

        return true;
    }

    static public function identify($src_file)
    {
        if (empty($src_file))
            throw new imageLibException('No source file argument passed');

        $hash = sha1($src_file);

        if (array_key_exists($hash, self::$cache))
        {
            return self::$cache[$hash];
        }

        $image = false;

        if (self::canUseImlib())
        {
            $im = @imlib_load_image($src_file);

            if ($im)
            {
                $image = array(
                    'format'    =>  strtoupper(imlib_image_format($im)),
                    'width'     =>  imlib_image_get_width($im),
                    'height'    =>  imlib_image_get_height($im),
                );

                imlib_free_image($im);
            }

            unset($im);
        }

        if (!$image && self::canUseImagick())
        {
            try {
                $im = new Imagick($src_file);

                if ($im)
                {
                    $image = array(
                        'width'     =>  $im->getImageWidth(),
                        'height'    =>  $im->getImageHeight(),
                        'format'    =>  strtoupper($im->getImageFormat()),
                    );

                    $im->destroy();
                }

                unset($im);
            }
            catch (ImagickException $e)
            {
            }

        }

        if (!$image && self::canUseGD())
        {
            $gd_img = getimagesize($src_file);

            if (!$gd_img)
                return false;

            $image['width'] = $gd_img[0];
            $image['height'] = $gd_img[1];

            switch ($gd_img[2])
            {
                case IMAGETYPE_GIF:
                    $image['format'] = 'GIF';
                    break;
                case IMAGETYPE_JPEG:
                    $image['format'] = 'JPEG';
                    break;
                case IMAGETYPE_PNG:
                    $image['format'] = 'PNG';
                    break;
                default:
                    $image['format'] = false;
                    break;
            }
        }

        self::$cache[$hash] = $image;

        return $image;
    }

    static public function resize($src_file, $dst_file, $new_width, $new_height=null, $options=array())
    {
        if (empty($src_file))
            throw new imageLibException('No source file argument passed');

        if (empty($dst_file))
            throw new imageLibException('No destination file argument passed');

        if (empty($new_width))
            throw new imageLibException('Needs at least the new width as argument');

        self::parseOptions($options);

        if (self::option(self::ENABLE_REPORT))
        {
            self::$report = array(
                'engine_used'   =>  '',
                'time_taken'    =>  0,
                'start_time'    =>  microtime(true),
            );
        }

        if (!$new_height)
        {
            $new_height = $new_width;
        }

        $new_height = (int) $new_height;
        $new_width = (int) $new_width;

        $lib = false;

        if (self::option(self::FORCE_IMLIB))
        {
            if (!self::canUseImlib())
                throw new imageLibException('Imlib seems not installed');

            $lib = self::IMLIB;
        }
        elseif (self::option(self::FORCE_GD))
        {
            if (!self::canUseGD())
                throw new imageLibException('GD seems not installed');

            $lib = self::GD;
        }
        elseif (self::option(self::FORCE_IMAGICK))
        {
            if (!self::canUseImagick())
                throw new imageLibException('Imagick seems not installed');

            $lib = self::IMAGICK;
        }

        if (self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR))
        {
            if ($lib == self::IMLIB)
            {
                throw new imageLibException("You can't use Imlib to force image size width background color.");
            }

            if (!$lib && self::canUseImagick())
            {
                $lib = self::IMAGICK;
            }
            elseif (!$lib && self::canUseGD())
            {
                $lib = self::GD;
            }
            elseif (!$lib)
            {
                throw new imageLibException("You need GD or Imagick to force image size using background color.");
            }
        }

        if (!$lib)
        {
            if (self::canUseImlib())
                $lib = self::IMLIB;
            elseif (self::canUseImagick())
                $lib = self::IMAGICK;
            elseif (self::canUseGD())
                $lib = self::GD;
        }

        if (empty($lib))
        {
            throw new imageLibException('No usable image library found');
        }

        if ($lib == self::IMLIB)
        {
            $res = self::imlibResize($src_file, $dst_file, $new_width, $new_height);
        }
        elseif ($lib == self::IMAGICK)
        {
            $res = self::imagickResize($src_file, $dst_file, $new_width, $new_height);
        }
        elseif ($lib == self::GD)
        {
            $res = self::gdResize($src_file, $dst_file, $new_width, $new_height);
        }

        if (self::option(self::ENABLE_REPORT))
        {
            if ($lib == self::IMLIB)
                self::$report['engine_used'] = 'imlib';
            elseif ($lib == self::IMAGICK)
                self::$report['engine_used'] = 'imagick';
            elseif ($lib == self::GD)
                self::$report['engine_used'] = 'gd';

            self::$report['time_taken'] = microtime(true) - self::$report['start_time'];
            unset(self::$report['start_time']);
        }

        return $res;
    }

    static public function getReport()
    {
        return self::$report;
    }

    static protected function getCropGeometry($w, $h, $new_width, $new_height)
    {
        $proportion_src = $w / $h;
        $proportion_dst = $new_width / $new_height;

        $x = $y = 0;
        $out_w = $w;
        $out_h = $h;

        if ($proportion_src > $proportion_dst)
        {
            $out_w = $h * $proportion_dst;
            $x = round(($w - $out_w) / 2);
        }
        else
        {
            $out_h = $w / $proportion_dst;
            $y = round(($h - $out_h) / 2);
        }

        return array($x, $y, round($out_w), round($out_h));
    }

    static protected function imlibResize($src_file, $dst_file, $new_width, $new_height)
    {
        $src = @imlib_load_image($src_file);

        if (!$src)
            return false;

        if ($format = self::option(self::FORCE_OUTPUT_FORMAT))
            $type = strtolower($format);
        else
            $type = strtolower(imlib_image_format($src));

        $w = imlib_image_get_width($src);
        $h = imlib_image_get_height($src);

        if (self::option(self::CROP))
        {
            list($x, $y, $w, $h) = self::getCropGeometry($w, $h, $new_width, $new_height);

            $dst = imlib_create_cropped_scaled_image($src, $x, $y, $w, $h, $new_width, $new_height);
        }
        elseif (self::option(self::IGNORE_ASPECT_RATIO))
        {
            $dst = imlib_create_scaled_image($src, $new_width, $new_height);
        }
        else
        {
            if ($w > $h)
                $new_height = 0;
            else
                $new_width = 0;

            $dst = imlib_create_scaled_image($src, $new_width, $new_height);
        }

        imlib_free_image($src);

        if ($type == 'png')
        {
            $png_compression = (int) self::option(self::PNG_COMPRESSION);

            if (empty($png_compression))
                $png_compression = self::$default_png_compression;

            imlib_image_set_format($dst, 'png');
            $res = imlib_save_image($dst, $dst_file, $err, (int)$png_compression);
        }
        elseif ($type == 'gif')
        {
            imlib_image_set_format($dst, 'gif');
            $res = imlib_save_image($dst, $dst_file);
        }
        else
        {
            $jpeg_quality = (int) self::option(self::JPEG_QUALITY);

            if (empty($jpeg_quality))
                $jpeg_quality = self::$default_jpeg_quality;

            imlib_image_set_format($dst, 'jpeg');
            $res = imlib_save_image($dst, $dst_file, $err, (int)$jpeg_quality);
        }

        $w = imlib_image_get_width($dst);
        $h = imlib_image_get_height($dst);

        imlib_free_image($dst);

        return ($res ? array($w, $h) : $res);
    }

    static protected function imagickResize($src_file, $dst_file, $new_width, $new_height)
    {
        try {
            $im = new Imagick($src_file);
        }
        catch (ImagickException $e)
        {
            return false;
        }

        if ($format = self::option(self::FORCE_OUTPUT_FORMAT))
            $type = strtolower($format);
        else
            $type = strtolower($im->getImageFormat());

        $im->setImageFormat($type);

        if (self::option(self::CROP))
        {
            $im->cropThumbnailImage($new_width, $new_height);
        }
        elseif (self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR))
        {
            if (self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR) == self::TRANSPARENT_COLOR)
                $c = new ImagickPixel('transparent');
            elseif (strlen(self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR)) != 6)
                $c = new ImagickPixel('#' . self::$default_background_color);
            else
                $c = new ImagickPixel('#' . self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR));

            $im->thumbnailImage($new_width, $new_height, true);

            $bg = new Imagick;
            $bg->newImage($new_width, $new_height, $c, 'png');

            $geometry = $im->getImageGeometry();

            /* The overlay x and y coordinates */
            $x = ($new_width - $geometry['width']) / 2;
            $y = ($new_height - $geometry['height']) / 2;

            $bg->compositeImage($im, imagick::COMPOSITE_OVER, $x, $y);
            $im->destroy();
            $im = $bg;
            unset($bg);
        }
        else
        {
            $im->thumbnailImage($new_width, $new_height, !self::option(self::IGNORE_ASPECT_RATIO));
        }

        if ($type == 'png')
        {
            $png_compression = (int) self::option(self::PNG_COMPRESSION);

            if (empty($png_compression))
                $png_compression = self::$default_png_compression;

            $im->setImageFormat('png');
            $im->setCompression(Imagick::COMPRESSION_LZW);
            $im->setCompressionQuality($png_compression * 10);
        }
        elseif ($type == 'gif')
        {
            $im->setImageFormat('gif');
        }
        else
        {
            $jpeg_quality = (int) self::option(self::JPEG_QUALITY);

            if (empty($jpeg_quality))
                $jpeg_quality = self::$default_jpeg_quality;

            $im->setImageFormat('jpeg');
            $im->setCompression(Imagick::COMPRESSION_JPEG);
            $im->setCompressionQuality($jpeg_quality);
        }

        $res = file_put_contents($dst_file, $im);

        $w = $im->getImageWidth();
        $h = $im->getImageHeight();

        $im->destroy();

        return ($res ? array($w, $h) : $res);
    }

    static protected function gdResize($src_file, $dst_file, $new_width, $new_height)
    {
        $infos = self::identify($src_file);

        if (!$infos)
            return false;

        if (self::option(self::FORCE_OUTPUT_FORMAT))
            $type = self::option(self::FORCE_OUTPUT_FORMAT);
        else
            $type = $infos['format'];

        try
        {
            switch ($infos['format'])
            {
                case 'JPEG':
                    $src = imagecreatefromjpeg($src_file);
                    break;
                case 'PNG':
                    $src = imagecreatefrompng($src_file);
                    break;
                case 'GIF':
                    $src = imagecreatefromgif($src_file);
                    break;
                default:
                    return false;
            }

            if (!$src)
                throw new Exception("No source image created");
        }
        catch (Exception $e)
        {
            throw new imageLibException("Invalid input format: ".$e->getMessage());
        }

        $w = $infos['width'];
        $h = $infos['height'];

        $dst_x = 0;
        $dst_y = 0;
        $src_x = 0;
        $src_y = 0;
        $dst_w = $new_width;
        $dst_h = $new_height;
        $src_w = $w;
        $src_h = $h;
        $out_w = $new_width;
        $out_h = $new_height;

        if (self::option(self::CROP))
        {
            list($src_x, $src_y, $src_w, $src_h) = self::getCropGeometry($w, $h, $new_width, $new_height);
        }
        elseif (!self::option(self::IGNORE_ASPECT_RATIO))
        {
            if ($w <= $new_width && $h <= $new_height)
            {
                $dst_w = $out_w = $w;
                $dst_h = $out_h = $h;
            }
            else
            {
                $in_ratio = $w / $h;
                $out_ration = $new_width / $new_height;

                $pic_width = $new_width;
                $pic_height = $new_height;

                if ($in_ratio >= $out_ration)
                {
                    $pic_height = $new_width / $in_ratio;
                }
                else
                {
                    $pic_width = $new_height * $in_ratio;
                }

                $dst_w = $out_w = $pic_width;
                $dst_h = $out_h = $pic_height;
            }
        }

        if (self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR))
        {
            $diff_width = $new_width - $dst_w;
            $diff_height = $new_height - $dst_h;
            $offset_x = $diff_width / 2;
            $offset_y = $diff_height / 2;

            $dst_x = round($offset_x);
            $dst_y = round($offset_y);
            $out_w = $new_width;
            $out_h = $new_height;
        }

        $dst = imagecreatetruecolor($out_w, $out_h);

        if (!$dst)
        {
            return false;
        }

        imageinterlace($dst, 0);

        $use_background = false;

        if (self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR))
        {
            if (self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR) == self::TRANSPARENT_COLOR
                || strlen(self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR)) == 6)
                $use_background = self::option(self::FORCE_SIZE_USING_BACKGROUND_COLOR);
            else
                $use_background = self::$default_background_color;
        }

        if (!$use_background || $use_background == self::TRANSPARENT_COLOR)
        {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        }
        else
        {
            $color = imagecolorallocate($dst,
                hexdec(substr($use_background, 0, 2)),
                hexdec(substr($use_background, 2, 2)),
                hexdec(substr($use_background, 4, 2))
                );

            imagefill($dst, 0, 0, $color);
        }


        if (self::option(self::USE_GD_FAST_RESIZE_TRICK))
        {
            fastimagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, 2);
        }
        else
        {
            imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }

        imagedestroy($src);

        try
        {
            if ($type == 'PNG')
            {
                $png_compression = (int) self::option(self::PNG_COMPRESSION);

                if (empty($png_compression))
                    $png_compression = self::$default_png_compression;

                $res = imagepng($dst, $dst_file, $png_compression, PNG_NO_FILTER);
            }
            elseif ($type == 'GIF')
            {
                $res = imagegif($dst, $dst_file);
            }
            else
            {
                $jpeg_quality = (int) self::option(self::JPEG_QUALITY);

                if (empty($jpeg_quality))
                    $jpeg_quality = self::$default_jpeg_quality;

                $res = imagejpeg($dst, $dst_file, $jpeg_quality);
            }

            imagedestroy($dst);
        }
        catch (Exception $e)
        {
            throw new imageLibException("Unable to create destination file: ".$e->getMessage());
        }

        return ($res ? array($dst_w, $dst_h) : $res);
    }

    static public function getImageStreamFormat($bytes)
    {
        $b = substr($bytes, 0, 4);

        switch ($b)
        {
            case 'GIF8':
                return 'GIF';
            case pack('H*', 'ffd8ffe0'):
                return 'JPEG';
            case pack('H*', '89504e47'):
                return 'PNG';
            default:
                return false;
        }
    }
}

function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3)
{
    // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
    // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
    // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
    // Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
    //
    // Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
    // Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
    // 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
    // 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
    // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
    // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
    // 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.

    if (empty($src_image) || empty($dst_image) || $quality <= 0)
    {
        return false;
    }

    if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h))
    {
        $temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
        imagecopyresized ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
        imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
        imagedestroy ($temp);
    }
    else
    {
        imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }

    return true;
}

?><?php
/**
    Fotoo Hosting
    Copyright 2010-2012 BohwaZ - http://dev.kd2.org/
    Licensed under the GNU AGPLv3

    This software is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This software is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this software. If not, see <http://www.gnu.org/licenses/>.
*/

error_reporting(E_ALL);

if (!version_compare(phpversion(), '5.3', '>='))
{
    die(__('You need at least PHP 5.2 to use this application.'));
}

if (!class_exists('SQLite3'))
{
    die(__('You need PHP SQLite3 extension to use this application.'));
}

define('UPLOAD_ERR_INVALID_IMAGE', 42);

class FotooException extends Exception {}

function exception_error_handler($errno, $errstr, $errfile, $errline )
{
    // For @ ignored errors
    if (error_reporting() === 0) return;
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler");

//require_once __DIR__ . '/lib-image/lib.image.php';

class Fotoo_Hosting_Config
{
    private $db_file = null;
    private $storage_path = null;

    private $base_url = null;
    private $storage_url = null;
    private $image_page_url = null;
    private $album_page_url = null;

    private $max_width = null;

    private $thumb_width = null;

    private $title = null;

    private $max_file_size = null;
    private $allow_upload = null;
    private $nb_pictures_by_page = null;

    private $admin_password = null;
    private $banned_ips = null;
	private $allow_private = null;
    private $ip_storage_expiration = null;

    public function __set($key, $value)
    {
        switch ($key)
        {
            case 'max_width':
            case 'thumb_width':
            case 'max_file_size':
            case 'nb_pictures_by_page':
            case 'ip_storage_expiration':
                $this->$key = (int) $value;
                break;
            case 'db_file':
            case 'storage_path':
            case 'base_url':
            case 'storage_url':
            case 'title':
            case 'image_page_url':
            case 'album_page_url':
            case 'admin_password':
                $this->$key = (string) $value;
                break;
            case 'banned_ips':
                $this->$key = (array) $value;
                break;
            case 'allow_private':
                $this->$key = (array) $value;
                break;
            case 'allow_upload':
                $this->$key = is_bool($value) ? (bool) $value : $value;
                break;
            case 'allowed_formats':
                if (is_string($value))
                {
                    $value = explode(',', strtoupper(str_replace(' ', '', $value)));
                }
                else
                {
                    $value = (array) $value;
                }

                // If Imagick is not present then we can't process images different than JPEG, GIF and PNG
                foreach ($value as $f=>$format)
                {
                    $format = strtoupper($format);

                    if ($format != 'PNG' && $format != 'JPEG' && $format != 'GIF' && !class_exists('Imagick'))
                    {
                        unset($value[$f]);
                    }
                }

                $this->$key = $value;

                break;
            default:
                throw new FotooException("Unknown configuration property $key");
        }
    }

    public function __get($key)
    {
        if (isset($this->$key))
            return $this->$key;
        else
            throw new FotooException("Unknown configuration property $key");
    }

    public function exportJSON()
    {
        $vars = get_object_vars($this);

        unset($vars['db_file']);
        unset($vars['storage_path']);
        unset($vars['admin_password']);

        return json_encode($vars);
    }

    public function exportPHP()
    {
        $vars = get_object_vars($this);

        $out = "<?php\n\n";
        $out.= '// Do not edit the line below';
        $out.= "\n";
        $out.= 'if (!isset($config) || !($config instanceof Fotoo_Hosting_Config)) die( __("Invalid call.") );';
        $out.= "\n\n";
        $out.= '// To edit one of the following configuration options, comment it out and change it';
        $out.= "\n\n";

        foreach ($vars as $key=>$value)
        {
            $out .= "// ".wordwrap($this->getComment($key), 70, "\n// ")."\n";
            $line = '$config->'.$key.' = '.var_export($value, true).";";

            if (strpos($line, "\n") !== false)
                $out .= "/*\n".$line."\n*/\n\n";
            else
                $out .= '#'.$line."\n\n";
        }

        $out.= "\n?>";

        return $out;
    }

    public function getComment($key)
    {
        switch ($key)
        {
            case 'max_width':       return __('Maximum image width or height, bigger images will be resized.');
            case 'thumb_width':     return __('Maximum thumbnail size, used for creating thumbnails.');
            case 'max_file_size':   return __('Maximum uploaded file size (in bytes). By default it\'s the maximum size allowed by the PHP configuration. See the FAQ for more informations.');
            case 'nb_pictures_by_page': return __('Number of images to display on each page in the pictures list.');
            case 'db_file':         return __('Path to the SQLite DB file.');
            case 'storage_path':    return __('Path to where the pictures are stored.');
            case 'base_url':        return __('URL of the webservice index.');
            case 'storage_url':     return __('URL to where the pictures are stored. Filename is added at the end.');
            case 'title':           return __('Title of the service.');
            case 'image_page_url':  return __('URL to the picture information page, hash is added at the end.');
            case 'album_page_url':  return __('URL to the album page, hash is added at the end.');
            case 'allow_upload':    return __('Allow upload of files? You can use this to restrict upload access. Can be a boolean or a PHP callback. See the FAQ for more informations.');
            case 'admin_password':  return __('Password to access admin UI? (edit/delete files, see private pictures)');
            case 'banned_ips':      return __('List of banned IP addresses (netmasks and wildcards accepted, IPv6 supported)');
            case 'allow_private':   return __('List of groups for which private images are allowed');
            case 'allowed_formats': return __('Allowed formats, separated by a comma');
            case 'ip_storage_expiration':
                                    return __('Expiration (in days) of IP storage, after this delay IP addresses will be removed from database');
            default: return '';
        }
    }

    public function __construct()
    {
        // Defaults
        $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http' ;
        $this->db_file = dirname(__FILE__) . '/datas.db';
        $this->storage_path = dirname(__FILE__) . '/i/';
        $this->base_url = $is_https . '://'. $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

        if ($this->base_url[strlen($this->base_url) - 1] != '/')
            $this->base_url .= '/';

        $this->storage_url = $this->base_url . str_replace(dirname(__FILE__) . '/', '', $this->storage_path);
        $this->image_page_url = $this->base_url . '?';
        $this->album_page_url = $this->base_url . '?a=';

        if (substr(basename($_SERVER['PHP_SELF']), 0, 5) != 'index')
            $this->base_url .= basename($_SERVER['PHP_SELF']);

        $this->max_width = 1920;
        $this->thumb_width = 320;

        $this->title = 'Fotoo Image Hosting service';

        $size = self::return_bytes(ini_get('upload_max_filesize'));
        $post = self::return_bytes(ini_get('post_max_size'));

        if ($post < $size)
            $size = $post;

        $memory = self::return_bytes(ini_get('memory_limit'));

        if ($memory < $size)
            $size = $memory;

        $this->max_file_size = $size;
        $this->allow_upload = true;
        $this->admin_password = 'fotoo';
        $this->banned_ips = [];
        $this->allow_private = [];
        $this->ip_storage_expiration = 366;
        $this->nb_pictures_by_page = 20;

        $this->allowed_formats = array('PNG', 'JPEG', 'GIF', 'SVG', 'XCF');
    }

    static public function return_bytes ($size_str)
    {
        switch (substr($size_str, -1))
        {
            case 'G': case 'g': return (int)$size_str * pow(1024, 3);
            case 'M': case 'm': return (int)$size_str * pow(1024, 2);
            case 'K': case 'k': return (int)$size_str * 1024;
            default: return $size_str;
        }
    }
}

function escape($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'utf-8', false);
}

//require __DIR__ . '/class.fotoo_hosting.php';

$config = new Fotoo_Hosting_Config;

$config_file = __DIR__ . '/config.php';

if (file_exists($config_file))
{
    require_once $config_file;
}
elseif (isset($_GET['create_config']))
{
    file_put_contents($config_file, $config->exportPHP());
    die( __("Default configuration created in config.php file, edit it to change default values.") );
}

// Check upload access
if (!is_bool($config->allow_upload) && is_callable($config->allow_upload))
{
    $config->allow_upload = (bool) call_user_func($config->allow_upload);
}

$fh = new Fotoo_Hosting($config);

if ($fh->isClientBanned())
{
    $fh->setBanCookie();
}

if (!empty($_GET['delete']))
{
    $id = !empty($_GET['c']) ? trim($_GET['c']) : false;

    if ($fh->remove($_GET['delete'], $id))
    {
		$page = ( !empty($_GET['page']) && is_numeric($_GET['page']) ) ? '='.(int) $_GET['page'] : '' ;
		$where = isset ( $_GET['mesphotos'] ) ? '?list'.$page.'&mesphotos' : '?list'.$page ;
		$where = isset ( $_GET['a'] ) ? '?a=' . $_GET['a'] : $where ;
        $where = isset ( $_GET['uploadedPicture'] ) ? '' : $where ;
        header('Location: '.$config->base_url.$where);
    }
    else
    {
        echo __('Can\'t delete picture');
    }

    exit;
}

if (!empty($_GET['textarea_name']))
{
    $id = !empty($_GET['c']) ? trim($_GET['c']) : false;

    if ($fh->remove($_GET['delete'], $id))
    {
        $where = isset ( $_GET['mesphotos'] ) ? '&mesphotos' : '' ;
        header('Location: '.$config->base_url.'?list'.$where);
    }
    else
    {
        echo __('Can\'t delete picture');
    }

    exit;
}

if (!empty($_GET['rotate']))
{
    $id = !empty($_GET['c']) ? trim($_GET['c']) : false;
    $hash = $_GET['rotate'];

    if ($fh->rotate($_GET['rotate'], $id))
    {

        $page = ( !empty($_GET['page']) && is_numeric($_GET['page']) ) ? '='.(int) $_GET['page'] : '' ;
        $where = isset ( $_GET['mesphotos'] ) ? '?list'.$page.'&mesphotos' : '' ;
        $where = isset ( $_GET['logged'] ) ? '?list'.$page : $where ;
        $where = isset ( $_GET['img'] ) ? '?' . $hash : $where ;
        $where = isset ( $_GET['a'] ) ? '?a=' . filter_var($_GET['a'], FILTER_SANITIZE_STRING) . '' : $where ;

        header('Location: '.$config->base_url.$where);
    }
    else
    {
        echo __('Can\'t rotate picture');
    }
    exit;
}
elseif (!empty($_GET['deleteAlbum']))
{
    $id = !empty($_GET['c']) ? trim($_GET['c']) : false;

    if ($fh->removeAlbum($_GET['deleteAlbum'], $id))
    {
	$page = ( !empty($_GET['page']) && is_numeric($_GET['page']) ) ? '='.(int) $_GET['page'] : '' ;
        $where = isset ( $_GET['mesalbums'] ) ? '?albums'.$page.'&mesalbums' : '?albums'.$page ;
        $where = isset ( $_GET['uploadedAlbum'] ) ? '?album' : $where ;
        header('Location: ' . $config->base_url.$where);
    }
    else
    {
        echo __('Can\'t delete album');
    }

    exit;
}
elseif (!empty($_POST['delete_albums']) && $fh->logged())
{
    foreach ($_POST['albums'] as $album)
    {
        $fh->removeAlbum($album, null);
    }

    header('Location: ' . $config->base_url . '?albums');
    exit;
}
elseif (!empty($_POST['delete_pictures']) && $fh->logged())
{
    foreach ($_POST['pictures'] as $pic)
    {
        $fh->remove($pic, null);
    }

    header('Location: ' . $config->base_url . '?list');
    exit;
}

if (isset($_POST['album_create']))
{
    if (!empty($_POST['title']))
    {
        try {
            $id = $fh->createAlbum($_POST['title'], empty($_POST['private']) ? false : true);
            echo "$id/" . $fh->makeRemoveId($id);
            exit;
        }
        catch (FotooException $e)
        {
            header('HTTP/1.1 400 Bad Request', true, 400);
            die( __("Upload not permitted.") );
        }
    }

    header('HTTP/1.1 400 Bad Request', true, 400);
    die( __("Bad Request") );
}

if (isset($_POST['album_append']))
{
    if (!empty($_POST['album']) && !empty($_POST['content']) && isset($_POST['name']) && isset($_POST['filename']))
    {
        if ($fh->appendToAlbum($_POST['album'], $_POST['name'], array('content' => $_POST['content'], 'name' => $_POST['filename'])))
        {
            echo __('OK');
        }
        else
        {
            echo __('FAIL');
        }
        exit;
    }

    header('HTTP/1.1 400 Bad Request', true, 400);
    die( __("Bad Request") );
}

if (isset($_GET['upload']))
{
    $error = false;

    if (empty($_FILES['upload']) && empty($_POST['upload']))
    {
        $error = UPLOAD_ERR_INI_SIZE;
    }
    else
    {
        try {
            $res = $fh->upload(!empty($_FILES['upload']) ? $_FILES['upload'] : $_POST['upload'],
                isset($_POST['name']) ? trim($_POST['name']) : '',
                isset($_POST['private']) ? (bool) $_POST['private'] : false
            );
        }
        catch (FotooException $e)
        {
            if ($e->getCode())
                $error = $e->getCode();
            else
                throw $e;
        }
    }

    if ($error)
    {
        $url = $config->base_url . '?error=' . $error;
        header('Location: ' . $url);
        exit;
    }
    else
    {
        $img = $fh->get($res);
        $url = $fh->getUrl($img, true);

        header('Location: ' . $url . '&uploadedPicture');

        exit;
    }
}

$html = $title = '';

if (isset($_GET['logout']))
{
    $fh->logout();
    header('Location: ' . $config->base_url);
    exit;
}
elseif (isset($_GET['login']))
{
    $title = __('Login');
    $error = '';

    if (!empty($_POST['password']))
    {
        if ($fh->login(trim($_POST['password'])))
        {
            header('Location: ' . $config->base_url);
            exit;
        }
        else
        {
            $error = '<p class="error">' . __('Wrong password') .  '.</p>';
        }
    }

    $html = '
        <article class="browse">
            <h2>'.$title.'</h2>
            '.$error.'
            <form method="post" action="' . $config->base_url . '?login">
            <fieldset>
                <dl>
                    <dt><label for="f_password">' . __('Password') . '</label></dt>
                    <dd><input type="password" name="password" id="f_password" /></dd>
                </dl>
            </fieldset>
            <p class="submit">
                <input type="submit" id="f_submit" value="' . __('Login') . '" />
            </p>
            </form>
        </article>
    ';
}
elseif (isset($_GET['list']))
{

	if ( isset($_GET['mesphotos']) ) {
		$title = __('Browse my pictures') ;
		$fh->unsetcookieFotooModo();
			} else {
		$title = __('Browse pictures') ;
		$fh->setcookieFotooModo();
	}

    if (!empty($_GET['list']) && is_numeric($_GET['list']))
        $page = (int) $_GET['list'];
    else
        $page = 1;

    $list = $fh->getList($page);
    $max = $fh->countList();

    $html = '';

    if ($fh->logged())
    {

		if ( !isset($_GET['mesphotos']) ) :
        	$html .= '
			<form method="post" action="" onsubmit="return confirm(\'' . __('Delete all the checked pictures') . '?\');">
				<p class="admin">
            		<input type="button" value="' . __('Check / uncheck all') . '" onclick="var l = this.form.querySelectorAll(\'input[type=checkbox]\'), s = l[0].checked; for (var i = 0; i < l.length; i++) { l[i].checked = s ? false : true; }" />
        		</p>';
		endif;

    }

    $html .= '
        <article class="browse">
            <h2>'.$title.'</h2>';

    $i=0;
    foreach ($list as &$img)
    {
    $i++;
        $thumb_url = $fh->getImageThumbUrl($img);
        $thumb_author_button = $fh->getImageAuthorBouton($img);
        $url = $fh->getUrl($img);

        $label = $img['filename'] ? escape(preg_replace('![_-]!', ' ', $img['filename'])) : 'View image';

		$html .= '
        <figure>
            <div class="img"><a href="'.$url.'">'.($img['private'] ? '<span class="private">' . __('Private') . '</span>' : '').($img['import'] ? '<span class="import">Import</span>' : '').'<img src="'.$thumb_url.refresh().'" alt="'.$label.'" /></a></div>
			<figcaption><div class="meta title"><h3>'.$label.'</h3></div></figcaption>';

        $img_url = $fh->getImageUrl($img);
        $thumb_url = $fh->getImageThumbUrl($img);
		$width = null != ($fh->getImageWidth($img)) ? $fh->getImageWidth($img) : '' ;
		$height = null != ($fh->getImageHeight($img)) ? $fh->getImageHeight($img) : '' ;
        $bbcode = '[url='.$img_url.'][img]'.$thumb_url.'[/img][/url]';
        $bbcodefullw = '[url='.$img_url.'][img='.$width.','.$height.']'.$img_url.'[/img][/url]';

		if ( ( isset($_GET['mesphotos']) || $fh->logged() ) ) {
			$mesphotos = isset($_GET['mesphotos']) ? '&amp;mesphotos' : '&amp;logged' ;
            $html .= '<ul class="rotate"><!--';
			if ( $img['format'] == 'JPEG' ) :
	            $html .= '
				    --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=90' . $mesphotos . '&amp;page='.$page.'">' . __('90°') . '</a></li><!--
	                --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=180' . $mesphotos . '&amp;page='.$page.'">' . __('180°') . '</a></li><!--
	                --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=270' . $mesphotos . '&amp;page='.$page.'">' . __('90°') . '</a></li><!--';
			endif;
            $html .= '--></ul>';
		}

		if ( isset($_GET['mesphotos']) ) {
            $html .= '
                <aside class="examples">
                <form name="bbform'.$i.'">
                    <label>BBCode miniature</label>
                    <input type="text" id="codetoinsert'.$i.'1" onclick="this.select();" value="'.escape($bbcode).'" />
                    <div id="insert'.$i.'1"></div>
                    <label>BBCode Image</label>
                    <input type="text" id="codetoinsert'.$i.'2" onclick="this.select();" value="'.escape($bbcodefullw).'" />
                    <div id="insert'.$i.'2"></div>
                </form>
                </aside>';

				// 	$html .= '
				// 	<p class="admin">
				// 	<a href="?delete='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;mesphotos&amp;page='.$page.'" onclick="return confirm(\'' . __('Really') . ' ?\n\n' . __('Merci de ne pas supprimer votre image si elle est encore appelée\nsur le forum ou une page du wiki.') . '\n\n\n\');">' . __('Delete picture') . '</a>
				// 	</p>';

        }

		$html .= $thumb_author_button ;

		$html .= '<p class="meta search"><a href="../search.php?action=search&keywords=' . $fh->getImageThumbUrlForSearch($img, $img['import']) . '&author=' . $img['punname'] . '&search_in=1&sort_by=0&sort_dir=DESC&show_as=posts&search=Valider" target="_blank">Rechercher</a></p>';

		if ( ($fh->isDeleteButtonDisplayed($img['date'],$img['punid']) && isset($_GET['mesphotos'])) || $fh->logged() ) {
			$where = isset ( $_GET['mesphotos'] ) ? '&amp;mesphotos' : '&amp;list' ;

			$html .= '<p class="admin">
					<a href="?delete='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']) . $where . '&amp;page='.$page.'" title="La suppression est autorisée pendant 24h après un téléchargement" onclick="return confirm(\'' . __('Really') . ' ?\n\n' . __('Merci de ne pas supprimer votre image si elle est encore appelée\nsur le forum ou une page du wiki.') . '\n\n\n\');">' . __('Delete picture') . '</a>
				</p>';
		}

		$author = null != ( $fh->getImageAuthor($img) ) ? $fh->getImageAuthor($img)  : '' ;
		// $punid = '';
		if ( !isset($_GET['mesphotos']) ) {
			if ($fh->logged() )
				// $punid = null != ( $fh->getImagePunID($img) ) ? ' / ' . $fh->getImagePunID($img) : '' ;
            $html .= '<label><input type="checkbox" name="pictures[]" value="' . escape($img['hash']) . '" /> ' . __('Delete') . '</label>';
		}

        $html .= '
        </figure>';
    }

    $html .= '
        </article>';


    if ( $fh->logged() && !isset($_GET['mesphotos']) )
    {
        $html .= '
	        <p class="admin submit">
	            <input type="submit" name="delete_pictures" value="' . __('Delete checked pictures') . '" />
	        </p>
		</form>';
    }

    if ($max > $config->nb_pictures_by_page)
    {
        $max_page = ceil($max / $config->nb_pictures_by_page);
        $html .= '
        <nav class="pagination">
            <ul>
        ';

	$mesphotos = isset($_GET['mesphotos']) ? '&mesphotos' : '' ; ;

        for ($p = 1; $p <= $max_page; $p++)
        {
            $html .= '<li'.($page == $p ? ' class="selected"' : '').'><a href="?list='.$p.$mesphotos.'">'.$p.'</a></li>';
        }

        $html .= '
            </ul>
        </nav>';
    }
}
elseif (isset($_GET['albums']))
{

	if ( isset($_GET['mesalbums']) ) {
		$title = __('Browse my albums') ;
		$fh->unsetcookieFotooModo();
	} else {
		$title = __('Browse albums') ;
		$fh->setcookieFotooModo();
	}

    if (!empty($_GET['albums']) && is_numeric($_GET['albums']))
        $page = (int) $_GET['albums'];
    else
        $page = 1;

    $list = $fh->getAlbumList($page);
    $max = $fh->countAlbumList();

    $html = '';

    if ($fh->logged() && !isset($_GET['mesalbums']))
    {
        $html .= '<form method="post" action="" onsubmit="return confirm(\'' . __('Delete all the checked albums') . '?\');">
        <p class="admin">
            <input type="button" value="' . __('Check / uncheck all') . '" onclick="var l = this.form.querySelectorAll(\'input[type=checkbox]\'); var s = l[0].checked; for (var i = 0; i < l.length; i++) { l[i].checked = s ? false : true; }" />
        </p>';
    }

    $html .= '
        <article class="albums">
            <h2>'.$title.'</h2>';

    $i=0;
    foreach ($list as $album)
    {
    $i=$i+1;
        $url = $config->album_page_url . $album['hash'];
        $nb = $fh->countAlbumPictures($album['hash']);

		if ( $nb <= 0 ) {
			$fh->removeAlbumEmpty($album['hash'], $fh->makeRemoveId($album['hash']));
		} else {

	        $html .= '
	        <figure>
	            <div class="meta title"><h3>'.escape($album['title']).'</h3><strong>('.$nb.' pictures)</strong></div><!--Check user id : ' . $album['punid'] . '-->
	            <a href="'.$url.'">'.($album['private'] ? '<span class="private">' . __('Private') . '</span>' : '');

	        foreach ($album['extract'] as $img)
	        {
	            $thumb_url = $fh->getImageThumbUrl($img);
	            $html .= '<div class="img"><img src="'.$thumb_url.refresh().'" alt="" /></div>';
	        }

	        $html .= '</a>';

	        if ( isset($_GET['mesalbums']) ) {

	    $listAlbumIMages = $fh->getAlbumPictures($album['hash'], $page);
	    $bbcode = '[b][url=' . $config->album_page_url . $album['hash'] . ']' . $album['title'] . "[/url][/b]\n";


	    foreach ($listAlbumIMages as $img)
	    {
	        $label = $img['filename'] ? escape(preg_replace('![_-]!', ' ', $img['filename'])) : 'View image';
	        $bbcode .= '[url='.$fh->getImageUrl($img).'][img]'.$fh->getImageThumbUrl($img)."[/img][/url] ";
	    }

	    $html .= '
	            <aside class="examples">
	            <form name="bbform'.$i.'">
	                <label>' . __('All pictures for a forum') . ' (BBCode)</label>
	                <textarea id="codetoinsert'.$i.'1" rows="1" onclick="this.select();">'.escape($bbcode).'</textarea>
	                <div id="insert'.$i.'1"></div>
	            </form>
	            </aside>';

			// 	$html .= '
			//         <p class="admin">
			//             <a href="?deleteAlbum='.rawurlencode($album['hash']).'&amp;c='.$fh->makeRemoveId($album['hash']).'&amp;mesalbums&amp;page='.$page.'" onclick="return confirm(\'' . __('Really') . ' ?\n\n' . __('Merci de ne pas supprimer votre album si il est encore appelé\nsur le forum ou une page du wiki.') . '\n\n\n\');">' . __('Delete album') . '</a>
			//         </p>';

	        }

			$album_author_button = $fh->getAlbumAuthorBouton($album['punname'], $album['punid'], strtotime($album['date']));
			$html .= '' . $album_author_button . '';

			if ( ( ( $fh->isDeleteButtonDisplayed(strtotime($album['date']), $album['punid']) ) && isset($_GET['mesalbums'])) || $fh->logged() ) {
				$where = isset ( $_GET['mesalbums'] ) ? '&amp;mesalbums' : '' ;

				$html .= '
			        <p class="admin">
			            <a href="?deleteAlbum='.rawurlencode($album['hash']).'&amp;c='.$fh->makeRemoveId($album['hash']) . $where . '&amp;page='.$page.'" title="La suppression est autorisée pendant 24h après un téléchargement" onclick="return confirm(\'' . __('Really') . ' ?\n\n' . __('Merci de ne pas supprimer votre album si il est encore appelé\nsur le forum ou une page du wiki.') . '\n\n\n\');">' . __('Delete album') . '</a>
			        </p>';
			}

	        if ($fh->logged() && !isset($_GET['mesalbums']) )
	        {
	            $html .= '<label><input type="checkbox" name="albums[]" value="' . escape($album['hash']) . '" /> ' . __('Delete') . '</label>';
	        }

	        $html .= '
	        </figure>';

		}

    }

    $html .= '
        </article>';

    if ($fh->logged() && !isset($_GET['mesalbums']) )
    {
        $html .= '
        <p class="admin submit">
            <input type="submit" name="delete_albums" value="' . __('Delete checked albums') . '" />
        </p>
        </form>';
    }

    if ($max > round($config->nb_pictures_by_page / 2))
    {
        $max_page = ceil($max / round($config->nb_pictures_by_page / 2));
        $html .= '
        <nav class="pagination">
            <ul>
        ';

	$mesalbums = isset($_GET['mesalbums']) ? '&mesalbums' : '' ; ;

        for ($p = 1; $p <= $max_page; $p++)
        {
            $html .= '<li'.($page == $p ? ' class="selected"' : '').'><a href="'.$config->base_url.'?albums='.$p.$mesalbums.'">'.$p.'</a></li>';
        }

        $html .= '
            </ul>
        </nav>';
    }
}
elseif (!empty($_GET['a']))
{
    $album = $fh->getAlbum($_GET['a']);

    if (empty($album))
    {
        header('HTTP/1.1 404 Not Found', true, 404);
        echo '
            <h1>' . __('Not Found') . '</h1>
            <p><a href="'.$config->base_url.'">'.$config->title.'</a></p>
        ';
        exit;
    }

    $title = $album['title'];

    if (!empty($_GET['p']) && is_numeric($_GET['p']))
        $page = (int) $_GET['p'];
    else
        $page = 1;

    $list = $fh->getAlbumPictures($album['hash'], $page);
    $max = $fh->countAlbumPictures($album['hash']);

    $bbcode = '[b][url=' . $config->album_page_url . $album['hash'] . ']' . $album['title'] . "[/url][/b]\n";

    foreach ($list as $img)
    {
        $label = $img['filename'] ? escape(preg_replace('![_-]!', ' ', $img['filename'])) : 'View image';
        $bbcode .= '[url='.$fh->getImageUrl($img).'][img]'.$fh->getImageThumbUrl($img)."[/img][/url] ";
    }

    $html = '
        <article class="browse">
            <div class="meta page-title"><h2>'.escape($title).'</h2></div>
            <p class="info">
                ' . __('Uploaded on') . ' <time datetime="'.date(DATE_W3C, $album['date']).'">'.strftime('%c', $album['date']).'</time>
                | '.(int)$max. ' ' . __('picture') .((int)$max > 1 ? 's' : '').'
            </p>';

    foreach ($list as &$img)
    {
        $thumb_url = $fh->getImageThumbUrl($img);
        $url = $fh->getUrl($img);

        $label = $img['filename'] ? escape(preg_replace('![_-]!', ' ', $img['filename'])) : 'View image';

        $html .= '
        <figure>
            <div class="img"><a href="'.$url.'">'.($img['private'] ? '<span class="private">' . __('Private') . '</span>' : '').'<img src="'.$thumb_url.refresh().'" alt="'.$label.'" /></a></div>
            <figcaption><div class="meta title"<a href="'.$url.'">'.$label.'</a></div></figcaption>';

        if ( $img['punid'] == $GLOBALS['punid'] && $img['format'] == 'JPEG' ) :
            $html .= '
                <ul class="rotate"><!--
                    --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=90&amp;a='.rawurlencode($album['hash']).'">' . __('90°') . '</a></li><!--
                    --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=180&amp;a='.rawurlencode($album['hash']).'">' . __('180°') . '</a></li><!--
                    --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=270&amp;a='.rawurlencode($album['hash']).'">' . __('90°') . '</a></li><!--
                --></ul>';
        endif;

		if ( $fh->isDeleteButtonDisplayed($img['date'], $img['punid']) ) {
			$where = isset ( $_GET['mesphotos'] ) ? '&amp;mesphotos' : '&amp;list' ;
			$where = isset ( $_GET['a'] ) ? '&amp;a='.rawurlencode($album['hash']) : $where ;

			$html .= '
				<p class="admin">
					<a href="?delete='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']) . $where . '" title="La suppression est autorisée pendant 24h après un téléchargement" onclick="return confirm(\'' . __('Really') . ' ?\n\n' . __('Merci de ne pas supprimer votre image si elle est encore appelée\nsur le forum ou une page du wiki.') . '\n\n\n\');">' . __('Delete picture') . '</a>
				</p>';
		}

        $html .= '
        </figure>';
    }


	$album_author_button = $fh->getAlbumAuthorBouton($album['punname'], $album['punid'], $album['date']);
	$html .= $album_author_button;

	if ( $fh->isDeleteButtonDisplayed($album['date'], $album['punid']) )
    {
		$where = '';
		if(!isset($_COOKIE['FotooModo'])) {
			$where = isset($_GET['c']) ? '&amp;uploadedAlbum' : '' ;
			$where = !isset($_GET['c']) ? '&amp;mesalbums' : $where ;
		}

		$html .= '
        <p class="admin">
            <a href="?deleteAlbum='.rawurlencode($album['hash']).'&amp;c=' . $fh->makeRemoveId($album['hash']) . $where . '" onclick="return confirm(\'' . __('Really') . '?\');">' . __('Delete album') . '</a>
        </p>';
    }
    elseif (!empty($_GET['c']))
    {
        $url = $config->album_page_url . $album['hash']
            . (strpos($config->album_page_url, '?') !== false ? '&c=' : '?c=')
            . $fh->makeRemoveId($album['hash']);

        $html .= '
        <p class="admin">
            <a href="?deleteAlbum='.rawurlencode($album['hash']).'&amp;c='.rawurldecode($_GET['c']).$where.'" onclick="return confirm(\'' . __('Really') . '?\');">' . __('Delete album') . '</a>
        </p>';

    }

	if ($album['punid'] == $GLOBALS['punid'] ) :
    $html .= '
            <aside class="examples">
            <form name="bbform1">
                <!--<dt>' . __('Share this album using this URL') . ':</dt>
                <dd><input type="text" onclick="this.select();" value="'.escape($config->album_page_url . $album['hash']).'" /></dd>-->
                <dt>' . __('All pictures for a forum') . ' (BBCode):</dt>
                <dd><textarea id="codetoinsert11" rows="1" onclick="this.select();">'.escape($bbcode).'</textarea></dd>
                <div id="insert11"></div>
            </form>
            </aside>';
    endif;

    $html .= '
        </article>';

    if ($max > $config->nb_pictures_by_page)
    {
        $max_page = ceil($max / $config->nb_pictures_by_page);
        $html .= '
        <nav class="pagination">
            <ul>
        ';

        $url = $config->album_page_url . $album['hash'] . ((strpos($config->album_page_url, '?') === false) ? '?p=' : '&amp;p=');

        for ($p = 1; $p <= $max_page; $p++)
        {
            $html .= '<li'.($page == $p ? ' class="selected"' : '').'><a href="'.$url.$p.'">'.$p.'</a></li>';
        }

        $html .= '
            </ul>
        </nav>';
    }
}
elseif (isset($_GET['stats']))
{
	if (!$fh->logged())
		header('Location: '.$config->base_url);

	$stats = $fh->getStats();
	$title = 'Stats';

	if ( isset($_GET['stats']) && $_GET['stats'] == 'img' ) {
		$orderbyimg = 'orderby';
		$orderbysize = '';
	} else {
		$orderbyimg = '';
		$orderbysize = 'orderby';
	}

	$html .='
	<table>
		<thead>
			<tr>
				<th class="tcl" scope="col">id</th>
				<th class="tc2" scope="col">Pseudo</th>
				<th class="tc3 ' . $orderbysize . '" scope="col"><a href="?stats">Poids</a></th>
				<th class="tc3 ' . $orderbyimg . '" scope="col"><a href="?stats=img">Nb image</a></th>
				<th class="tc3" scope="col">Poids moyen</th>
			</tr>
		</thead>
	';
	$count = 0;
	foreach ($stats as $punid)
    {
		$rowclass = (++$count % 2) ? "rowodd" : "roweven" ;
		$id = $punid['punid'];
		$name = $punid['punname'];
		$size = round($punid['punstorage'] / 1024 / 1024, 2) . ' MB';
		$nbimage = $punid['nbimage'];
		$poidsmoyen = $punid['nbimage'] < 1 ? 0 : round($punid['punstorage'] / $nbimage / 1024, 2) . ' KB';
		$html .= '
		<tr class="' . $rowclass . '">
			<td class="tcl1"><a href="../profile.php?id='. $id .'">'. $id .'</a></td>
			<td class="tcl2">' . $name  . '</td>
			<td class="tcl3">'  . $size . '</td>
			<td class="tcl3">'  . $nbimage . '</td>
			<td class="tcl3">'  . $poidsmoyen . '</td>
		</tr>
	';
	}
	$html .= '
	</table>
	';
 }
elseif (!isset($_GET['album']) && !isset($_GET['error']) && !empty($_SERVER['QUERY_STRING']) && !isset($_GET['insert']) && !isset($_GET['stats']) && !isset($_GET['q']))
{
    $query = explode('.', $_SERVER['QUERY_STRING']);
    $hash = ($query[0] == 'r') ? $query[1] : $query[0];
    $img = $fh->get($hash);

    if (empty($img))
    {
        header('HTTP/1.1 404 Not Found', true, 404);
        echo '
            <h1>' . __('Not Found') . '</h1>
            <p><a href="'.$config->base_url.'">'.$config->title.'</a></p>
        ';
        exit;
    }

    $img_url = $fh->getImageUrl($img);

    if ($query[0] == 'r')
    {
        header('Location: '.$img_url);
        exit;
    }

    $url = $fh->getUrl($img);
    $thumb_url = $fh->getImageThumbUrl($img);
    $short_url = $fh->getShortImageUrl($img);
    $title = $img['filename'] ? $img['filename'] : 'Image';

    // Short URL auto discovery
    header('Link: <'.$short_url.'>; rel=shorturl');

	$size = null != $img['size'] ? $fh->getPictureSize($img['size']) : '' ;
	$width = null != ($fh->getImageWidth($img)) ? $fh->getImageWidth($img) : '' ;
	$height = null != ($fh->getImageHeight($img)) ? $fh->getImageHeight($img) : '' ;
    $bbcode = '[url='.$img_url.'][img]'.$thumb_url.'[/img][/url]';
    $bbcodefullw = '[url='.$img_url.'][img='.$width.','.$height.']'.$img_url.'[/img][/url]';
    $html_code = '<a href="'.$img_url.'"><img src="'.$thumb_url.refresh().'" alt="'.(trim($img['filename']) ? $img['filename'] : '').'" /></a>';

    $html = '
    <article class="picture">
        <header>
            '.(trim($img['filename']) ? '<div class="meta page-title"><h2>' . escape(strtr($img['filename'], '-_.', '   ')) . '</h2></div>' : '').'
            <p class="info">
                ' . __('Uploaded on') . ' <time datetime="'.date(DATE_W3C, $img['date']).'">'.strftime('%c', $img['date']).'</time>
                | Size: '.$img['width'].' × '.$img['height'].' ('.$img['format'].', '.$size.')
            </p>
        </header>
        <figure class="picture-visu">
            <a href="'.$img_url.'">'.($img['private'] ? '<span class="private">' . __('Private') . '</span>' : '').'<img src="'.$img_url.refresh().'" alt="'.escape($title).'" /></a>';

        if ( $img['punid'] == $GLOBALS['punid'] && $img['format'] == 'JPEG' ) :
        $html .= '
            <ul class="rotate"><!--
                --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=90&amp;img">' . __('90°') . '</a></li><!--
                --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=180&amp;img">' . __('180°') . '</a></li><!--
                --><li class="button"><a href="?rotate='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).'&amp;angle=270&amp;img">' . __('90°') . '</a></li><!--
            --></ul>';
        endif;

        $html .='
            </figure>';

    if (!empty($img['album']))
    {
        $prev = $fh->getAlbumPrevNext($img['album'], $img['hash'], -1);
        $next = $fh->getAlbumPrevNext($img['album'], $img['hash'], 1);
        $album = $fh->getAlbum($img['album']);

        $html .= '
        <footer class="context">';

        if ($prev)
        {
            $thumb_url = $fh->getImageThumbUrl($prev);
            $url = $fh->getUrl($prev);
            $label = $prev['filename'] ? escape(preg_replace('![_-]!', ' ', $prev['filename'])) : 'View image';

            $html .= '
            <figure class="prev">
                <div class="img"><a href="'.$url.'"><b>&larr;</b><img src="'.$thumb_url.refresh().'" alt="'.$label.'" /></a></div>
                <figcaption><a href="'.$url.'">'.$label.'</a></figcaption>
            </figure>';
        }
        else
        {
            $html .= '<figure class="prev"><!--<b>…</b>--></figure>';
        }

        $html .= '
            <figure>
                <h3>' . __('Album') . ':</h3>
                <h2><a href="' . $config->album_page_url . $album['hash'] . '"> ' . escape($album['title']) .'</a></h2></figure
            </figure>';

        if ($next)
        {
            $thumb_url = $fh->getImageThumbUrl($next);
            $url = $fh->getUrl($next);
            $label = $next['filename'] ? escape(preg_replace('![_-]!', ' ', $next['filename'])) : 'View image';

            $html .= '
            <figure class="prev">
                <div class="img"><a href="'.$url.'"><img src="'.$thumb_url.'" alt="'.$label.'" /><b>&rarr;</b></a></div>
                <figcaption><a href="'.$url.refresh().'">'.$label.'</a></figcaption>
            </figure>';
        }
        else
        {
            $html .= '<figure class="next"><!--<b>…</b>--></figure>';
        }

        $html .= '
            </footer>';
    }

	$thumb_author_button = $fh->getImageAuthorBouton($img);
	$html .= $thumb_author_button;

	$where = '';
	if(!isset($_COOKIE['FotooModo'])) {
		$where = '&amp;mesphotos' ;
		$where = isset($_GET['uploadedPicture']) ? '&amp;uploadedPicture' : $where ;
	}
	// $where .= isset ( $_GET['a'] ) ? '?a=' . $_GET['a'] : '' ;

	if(isset($album['title'])){
		$where = '&amp;a=' . $album['hash'] ;
	}

	if ( $fh->isDeleteButtonDisplayed($img['date'], $img['punid']) && empty($_GET['c']) )
    {
		if ($fh->logged() ) {
			$html .= '
			<p class="admin">
			' . __('IP address') . ': ' . escape(is_null($img['ip']) ?  __('Not available') : ($img['ip'] == 'R' ? __('Automatically removed from database'): $img['ip'])) . '
			</p>';
		}

		$html .=  '
		<p class="admin">
		<a href="?delete='.rawurlencode($img['hash']).'&amp;c='.$fh->makeRemoveId($img['hash']).$where.'" title="La suppression est autorisée pendant 24h après un téléchargement" onclick="return confirm(\'' . __('Really') . '?\');">' . __('Delete picture') . '</a>
		</p>';
    }
    elseif (!empty($_GET['c']))
    {
        $html .= '
        <p class="admin">
            <a href="?delete='.rawurlencode($img['hash']).'&amp;c='.rawurldecode($_GET['c']).$where.'" onclick="return confirm(\'' . __('Really') . '?\');">' . __('Delete picture') . '</a>
        </p>';
    }

        if ($img['punid'] == $GLOBALS['punid'] ) :
        $html .= '
        <aside class="examples">
        <form name="bbform1">
            <dt>BBCode miniature</dt>
            <dd><input id="codetoinsert11" type="text" onclick="this.select();" value="'.escape($bbcode).'" /></dd>
            <div id="insert11"></div>
            <dt>BBCode Image</dt>
            <dd><input id="codetoinsert12" type="text" onclick="this.select();" value="'.escape($bbcodefullw).'" /></dd>
            <div id="insert12"></div>
        </form>
        </aside>
        </article>';

    endif;

}
elseif (!$config->allow_upload)
{
    $html = '<p class="error">' . __('Uploading is not allowed') . '.</p>';
}
else
{
	$fh->unsetcookieFotooModo();

    $js_url = file_exists(__DIR__ . '/upload.js')
        ? $config->base_url . 'upload.js'
        : $config->base_url . '?js';

    $html = '
        <script type="text/javascript">
        var config = '.$config->exportJSON().';
        </script>';

    if (!empty($_GET['error']))
    {
        $html .= '<p class="error">'.escape(Fotoo_Hosting::getErrorMessage($_GET['error'])).'</p>';
    }

    if (isset($_GET['album']))
    {
        $html .= '
        <form method="post" action="'.$config->base_url.'?upload" id="f_upload">
        <article class="upload">
            <header>
                <h2>' . __('Upload an album') . '</h2>
                <p class="info">
                    ' . __('Maximum file size') . ': '.round($config->max_file_size / 1024 / 1024, 2).'MB
                    | ' . __('Image types accepted') . ': JPEG ' . __('only') . '
                </p>
            </header>
            <fieldset>
                <dl>
                    <dt><label for="f_files">' . __('Files') . ':</label></dt>

                    <dd id="f_file_container"><input type="file" name="upload" id="f_files" multiple="multiple" accept="image/jpeg" required="required" /></dd>

					<dt><label for="f_title">' . __('Title') . ':</label></dt>
                    <dd><input type="text" name="title" id="f_title" maxlength="100" required="required" /></dd>';

                    if ( $fh->isPrivateAllowed() ) {
                    $html .= '
                    <dt><label for="f_private">' . __('Private') . '</label></dt>
                    <dd class="private"><label><input type="checkbox" name="private" id="f_private" value="1" />
                        (' . __('If checked, this album won\'t appear in &quot;browse pictures&quot;') . ')</label></dd>';
                    } else {
                    $html .= '
                            <input type="hidden" name="private" id="f_private" value="1" />';
                   }

                   $html .= '

                </dl>
            </fieldset>
            <div id="albumParent">' . __('Please select some files') . '...</div>
            <p class="submit">
                <input type="submit" id="f_submit" value="' . __('Upload') . '" />
            </p>
        </article>
        </form>';
    }
    else
    {
        $html .= '
        <form method="post" enctype="multipart/form-data" action="'.$config->base_url.'?upload" id="f_upload">
        <article class="upload">
            <header>
                <h2>' . __('Upload a file') . '</h2>
                <p class="info">
                    ' . __('Maximum file size') . ': '.round($config->max_file_size / 1024 / 1024, 2).'MB
                    | ' . __('Image types accepted') . ': '.implode(', ', $config->allowed_formats).'
                </p>
            </header>
            <fieldset>
                <input type="hidden" name="MAX_FILE_SIZE" value="'.($config->max_file_size - 1024).'" />
                <dl>
                    <dt><label for="f_file">' . __('File') . ':</label></dt>
                    <dd id="f_file_container"><input type="file" name="upload" id="f_file" /></dd>
                    <dt><label for="f_name">' . __('Name') . ':</label></dt>
                    <dd><input type="text" name="name" id="f_name" maxlength="30" /></dd>';

                    if ( $fh->isPrivateAllowed() ) {
                    $html .= '
                    <dt><label for="f_private">' . __('Private') . '</label></dt>
                    <dd class="private"><label><input type="checkbox" name="private" id="f_private" value="1" />
                        (' . __('If checked, picture won\'t appear in pictures list') . ')</label></dd>';
                    } else {
                    $html .= '
                    	<input type="hidden" name="private" id="f_private" value="1" />';
                     }

                    $html .= '
                </dl>
            </fieldset>
            <div id="resizeParent"></div>
            <p class="submit">
                <input type="submit" id="f_submit" value="' . __('Upload') . '" />
            </p>
        </article>
        </form>';
    }

    $html .= '<script type="text/javascript" src="'.$js_url.'"></script>';
}

$css_url = file_exists(__DIR__ . '/style.css')
    ? $config->base_url . 'style.css'
    : $config->base_url . '?css';

if (file_exists(__DIR__ . '/user_header.php')) {
    require __DIR__ . '/user_header.php';
} else {
echo '<!DOCTYPE html>
<html>
<head>
    <meta name="charset" content="utf-8" />
    <title>'.($title ? escape($title) . ' - ' : '').$config->title.'</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" type="text/css" href="'.$css_url.'" />
</head>

<body>';
}
echo '
<div id="punindex" class="pun">

  <div class="punwrap">

    <div id="brdheader" class="block">

    	<div class="box">

    		<div id="brdtitle" class="inbox">
    			<h1><a href="'.$config->base_url.'">'.$config->title.'</a></h1>
        '.($fh->logged() ? '<h2 class="admin-mode">(admin mode)</h2>' : '').'
    		</div>

    		<div id="brdmenu" class="inbox">
    			<ul>
                <li><a href="'.$config->base_url.'">' . __('Upload a file') . '</a></li>
                <li><a href="'.$config->base_url.'?list&mesphotos">' . __('Mes images') . '</a></li>
                <li><a href="'.$config->base_url.'?albums&mesalbums">' . __('Mes albums') . '</a></li>
                <li class="hidden-from-ez-toolbar"><a href="'.$config->base_url.'?list">' . __('Browse pictures') . '</a></li>
                <li class="hidden-from-ez-toolbar"><a href="'.$config->base_url.'?albums">' . __('Browse albums') . '</a></li>
				'.($fh->logged() ? '<li class="hidden-from-ez-toolbar"><a href="'.$config->base_url.'?stats">' . __('Stats') . '</a></li>' : '').'
    			</ul>
    		</div>

    		<div id="brdwelcome" class="inbox">
    			<ul class="conl">
    				<li><span>Connecté(e) sous l\'identité&#160; <strong>' . $GLOBALS['punname'] .'</strong></span></li>
    			</ul>
    			<div class="clearer"></div>
    		</div>

    	</div>

    </div>

	' . Fotoo_Hosting::getInfoMessage() . '

    <div id="page">
        ' . $html . '
    </div>

<div id="alerte"></div>
<script>
if (sessionStorage.getItem("album_enabled")=="ok" && document.getElementById("albums") == null){
	var album_li = document.createElement("li");
	album_li.setAttribute("id", "albums");
	var album_a = document.createElement("a");
	album_a.href = "?album";
	album_a.innerHTML = "' .  __('Upload an album') . '";
	album_li.appendChild(album_a);
	var link = document.querySelector("#brdmenu ul li:nth-child(2)");
	link.parentNode.insertBefore(album_li, link);
}
</script>

<script>
function getUrlVars() {
var vars = {};
var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
vars[key] = value;
});
return vars;
}

var insert = getUrlVars()["insert"];

// Check browser support
if (typeof(Storage) !== "undefined") {
  // Store

    if (insert == 1) {

        document.body.className = "from-ez-toolbar";
        sessionStorage.setItem("textarea_name", "req_message");

        var field  = window.opener.document.getElementsByName("req_message")[0];
        field.focus();

        sessionStorage.setItem("startSelection", field.value.substring(0, field.selectionStart));
        sessionStorage.setItem("currentSelection", field.value.substring(field.selectionStart, field.selectionEnd));
        sessionStorage.setItem("endSelection", field.value.substring(field.selectionEnd));

}
  // Retrieve

    if (sessionStorage.getItem("textarea_name")=="req_message"){
        document.body.className = "from-ez-toolbar";';


for ($i = 1; $i <= $config->nb_pictures_by_page ; $i++) {
echo '
        if (document.getElementById(\'insert'.$i.'1\') !== null) {document.getElementById(\'insert'.$i.'1\').innerHTML = "<p class=\"buttons\"><input type=\"button\" value=\"Insérer\" onclick=\"insertThumb('.$i.',1)\"></p>";}
        if (document.getElementById(\'insert'.$i.'2\') !== null) {document.getElementById(\'insert'.$i.'2\').innerHTML = "<p class=\"buttons\"><input type=\"button\" value=\"Insérer\" onclick=\"insertThumb('.$i.',2)\"></p>";}';
}

echo '
}

//    <input type=\"button\" value=\"Annuler\" onclick=\"self.close()\">

//    if (document.getElementById("alerte") !== null && insert == 1) {
//        document.getElementById("alerte").innerHTML = sessionStorage.getItem("textarea_name") + sessionStorage.getItem("startSelection") + sessionStorage.getItem("currentSelection") + sessionStorage.getItem("endSelection");
//    }

} else {
  document.getElementById("alerte").innerHTML = "Sorry, your browser does not support Web Storage...";
}

	function insertThumb(MyForm,MyInput) {
        var field  = window.opener.document.getElementsByName(sessionStorage.getItem("textarea_name"))[0];
        var scroll = field.scrollTop;
        field.focus();

        /* === Part 1: get the selection === */
                var startSelection   = sessionStorage.getItem("startSelection");
                var currentSelection = sessionStorage.getItem("currentSelection");
                var endSelection     = sessionStorage.getItem("endSelection");

        /* === Part 2: creating tagged element === */
        var quote = document.forms["bbform"+MyForm].elements["codetoinsert"+MyForm+MyInput].value;
		currentSelection = quote;

        /* === Part 3: adding what was produced to the opener === */
                field.value = startSelection + currentSelection + endSelection;
                field.focus();

        field.scrollTop = scroll;
		self.close();
	}
</script>

  <footer>
      ' . __('Powered by Fotoo Hosting application from') . ' <a href="http://kd2.org/">KD2.org</a>
      | '.($fh->logged() ? '<a href="'.$config->base_url.'?logout">' . __('Logout') . '</a>' : '<a href="'.$config->base_url.'?login">' . __('Login') . '</a>').'
  </footer>

  </div><!-- .punwrap -->
</div><!-- #punindex -->';

if (file_exists(__DIR__ . '/user_footer.php'))
    require_once __DIR__ . '/user_footer.php';
else
{
    echo '
</body>
</html>';
}


?>
