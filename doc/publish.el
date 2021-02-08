;; publish.el --- Publish org-mode project on Gitlab Pages
;; Author: Rasmus

;;; Commentary:
;; This script will convert the org-mode files in this directory into
;; html.

;;; Code:

(require 'package)
(package-initialize)
(add-to-list 'package-archives '("org" . "https://orgmode.org/elpa/") t)
(add-to-list 'package-archives '("melpa" . "https://melpa.org/packages/") t)
(package-refresh-contents)
(package-install 'org-plus-contrib)
(setq org-confirm-babel-evaluate nil)

;; CSS
(require 'htmlize)
(setq org-html-htmlize-output-type 'css)
;; CSS

(require 'org)
(require 'ox-publish)

;; setting to nil, avoids "Author: x" at the bottom
(setq user-full-name nil)

(setq org-export-with-section-numbers nil
      org-export-with-smart-quotes t
      org-export-with-toc t)

(setq org-html-divs '((preamble "header" "top")
                      (content "main" "content")
                      (postamble "footer" "postamble"))
      org-html-container-element "section"
      org-html-metadata-timestamp-format "%Y-%m-%d"
      org-html-checkbox-type 'html
      org-html-html5-fancy t
      org-html-validation-link nil
      org-html-doctype "html5"
	  org-use-sub-superscripts nil
	  org-export-with-sub-superscripts nil
	  org-export-default-language "fr")

(defun org-img-export (path desc format)
  (cond
   ((eq format 'html)
	(format "<img src=\"%s\" alt=\"%s\"/>" path desc))
   ((eq format 'latex)
	(format "\\begin{center}\\includegraphics[width=.9\\linewidth]{%s}\\end{center}" path desc))))

(org-add-link-type "img" 'org-img-follow 'org-img-export)

(setq org-publish-project-alist
      (list
       (list "org"
             :base-directory "."
             :base-extension "org"
             :recursive t
             :publishing-function '(org-html-publish-to-html)
             :publishing-directory "../progression/app/html/doc/"
             :exclude (regexp-opt '("README" "draft"))
             :auto-sitemap t
             :sitemap-filename "index.org"
             :sitemap-file-entry-format "%d *%t*"
             :html-head-extra "<link rel=\"icon\" type=\"image/x-icon\" href=\"/favicon.ico\"/>"
             :sitemap-style 'list
			 )
       (list "images"
             :base-directory "images"
             :base-extension (regexp-opt '("jpg" "jpeg" "gif" "png" "svg" "ico"))
             :publishing-directory "../progression/app/html/doc/images"
             :publishing-function 'org-publish-attachment
             :recursive t)
       (list "site" :components '("org"))))

(provide 'publish)
;;; publish.el ends here
