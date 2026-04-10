<!-- Add Certificate Popup (separate form) -->
<style>
    /* Disabled state for generic save button */
    .save-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        filter: grayscale(0.2);
    }
</style>
<div id="addCertificatePopup" class="certificate-add-popup" style="display:none;">
    <div class="certificate-add">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Add New Certificate</div>

        <form id="addCertificateForm" method="post" action="<?= URLROOT; ?>/profile/addCertificate" enctype="multipart/form-data" class="certificate-form">
            <div class="form-group">
                <label for="certificateNameAdd">Name</label>
                <input type="text" id="certificateNameAdd" name="certificate_name" required>
            </div>
            <div class="form-group">
                <label for="certificateIssuerAdd">Issuing Organization</label>
                <input type="text" id="certificateIssuerAdd" name="certificate_issuer" required>
            </div>
            <div class="form-group">
                <label for="certificateDateAdd">Issue Date</label>
                <input type="date" id="certificateDateAdd" name="certificate_date" required>
            </div>
            <div class="form-group">
                <label for="certificateFileAdd">Upload Certificate (PDF)</label>
                <div class="file-upload-container">
                    <input type="file" id="certificateFileAdd" name="certificate_file" accept=".pdf" style="display:none;">
                    <button type="button" class="file-upload-btn" id="chooseFileBtnAdd" onclick="document.getElementById('certificateFileAdd').click()">Choose File</button>
                    <span class="file-name" id="fileNameAdd" style="color:var(--text)">No file chosen</span>
                </div>
                <span id="certAddTooLarge" style="display:none;color:red;font-size:12px;">Attached file is more than 5MB</span>
            </div>

            <div style="margin-top:12px;">
                <button type="submit" class="save-btn" id="saveNewBtnAdd">Save Certificate</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Certificate Popup (separate form) -->
<div id="editCertificatePopup" class="certificate-add-popup" style="display:none;">
    <div class="certificate-add">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title" id="certificateFormTitleEdit">Edit Certificate</div>

        <form id="editCertificateForm" method="post" action="<?= URLROOT; ?>/profile/updateCertificate" enctype="multipart/form-data" class="certificate-form">
            <input type="hidden" name="certificate_id" id="certificateIdEdit" value="">
            <div class="form-group">
                <label for="certificateNameEdit">Name</label>
                <input type="text" id="certificateNameEdit" name="certificate_name" required>
            </div>
            <div class="form-group">
                <label for="certificateIssuerEdit">Issuing Organization</label>
                <input type="text" id="certificateIssuerEdit" name="certificate_issuer" required>
            </div>
            <div class="form-group">
                <label for="certificateDateEdit">Issue Date</label>
                <input type="date" id="certificateDateEdit" name="certificate_date" required>
            </div>
            <div class="form-group">

                <div class="file-upload-container" id="fileUploadContainerEdit">
                    <label for="certificateFileEdit">Upload Certificate (PDF)</label>
                    </br>
                    <input type="file" id="certificateFileEdit" name="certificate_file" accept=".pdf" style="display:none;">
                    <button type="button" class="file-upload-btn" id="chooseFileBtnEdit" onclick="document.getElementById('certificateFileEdit').click()">Choose File</button>
                    <span class="file-name" id="fileNameEdit" style="color:var(--text)">No file chosen</span>
                    <input type="hidden" id="removeFileInputEdit" name="remove_certificate_file" value="0">
                    <input type="hidden" id="existingFileInputEdit" name="existing_certificate_file" value="">
                </div>

                <div id="currentFileContainerEdit" style="margin-top:8px; display:none;">
                    <span style="color:var(--muted)">Current file: </span>
                    <a href="#" id="currentFileLinkEdit" target="_blank" style="color:var(--link);"></a>
                    <button type="button" id="cutFileBtnEdit" class="file-cut-btn" title="Remove current file"
                        style="margin-left:12px;background:none;border:none;color:var(--link);cursor:pointer;font-size:1rem;">
                        &times;
                    </button>
                </div>
                <span id="certEditTooLarge" style="display:none;color:red;font-size:12px;">Attached file is more than 5MB</span>
            </div>

            <div style="margin-top:12px;">
                <button type="submit" class="save-btn" id="saveChangesBtnEdit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Delete Confirmation Popup (uses same styles as certificate-add-popup) -->
<div id="deleteCertificatePopup" class="certificate-add-popup" style="display:none;">
    <div class="certificate-add">
        <button class="close-popup" title="Close"><i class="fas fa-times"></i></button>
        <div class="form-title">Delete Certificate</div>
        <div class="certificate-delete-body" style="color:var(--text); padding:16px;">
            <p>Are you sure you want to permanently delete this certificate? This action cannot be undone.</p>
        </div>
        <div style="display:flex; gap:12px; justify-content:flex-end; padding:12px 16px 20px;">
            <button type="button" id="cancelDeleteCertBtn" class="save-btn" style="background:transparent;color:var(--text);border:1px solid var(--border);">Cancel</button>
            <button type="button" id="confirmDeleteCertBtn" class="save-btn" style="background:var(--danger);color:#fff;">Delete</button>
        </div>
    </div>
</div>