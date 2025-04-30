function showToastrErrorsOneByOne(errors) {
    let delay = 0;
    errors.forEach((err) => {
        setTimeout(() => {
            toastr.error(err);
        }, delay);
        delay += 1200; // 1.2s between each message
    });
}

toastr.options = {
    closeButton: true,
    progressBar: true,
    timeOut: "3000",
    positionClass: "toast-top-right",
};

(function ($) {
    $(document).ready(function () {
        $("#addContactForm").on("submit", function (e) {
            e.preventDefault();

            let $form = $(this);
            let formData = $form.serialize();

            $.ajax({
                url: $form.attr("action"),
                method: "POST",
                data: formData,
                success: function (response) {
                    let user = response.data;
                    let modalHtml = response.modal_html;

                    let rowNumber = $("#contactTableBody tr").length + 1;

                    let typesHtml = user.types
                        .map(function (type) {
                            return `<span class="badge badge-info">${type}</span>`;
                        })
                        .join(" ");

                    // Append new contact row
                    $("#contactTableBody").append(`
                        <tr>
                            <td>${rowNumber}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.phone}</td>
                            <td>${user.designation ?? ""}</td>
                            <td>${user.organization ?? ""}</td>
                            <td>${user.address ?? ""}</td>
                            <td>${user.created_at_human}</td>
                            <td>${user.created_by}</td>
                            <td>${user.updated_by ?? ""}</td>
                            <td>${typesHtml}</td>
                            <td>
                                <a class="btn btn-sm btn-warning" href="/contact-management/contact/${
                                    user.id
                                }/edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a class="btn btn-sm btn-danger" href="/contact-management/contact-trash/${
                                    user.id
                                }">
                                    <i class="fa fa-trash"></i>
                                </a>
                                <a class="btn btn-sm btn-success" href="/contact-management/contact/${
                                    user.id
                                }/logs" target="_blank">
                                    <i class="fa fa-file-text"></i>
                                </a>
                                <a class="btn btn-sm btn-primary" href="/contact-management/contact/${
                                    user.id
                                }/print" target="_blank">
                                    <i class="fa fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    `);

                    // ✅ Append the modal HTML to the page
                    $("body").append(modalHtml);

                    // ✅ Reset the form
                    $form[0].reset();
                    Swal.fire({
                        title: "Success!",
                        text: "Contact added successfully!",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },

                error: function (xhr) {
                    let res = xhr.responseJSON;
                    let errors = res?.errors;

                    if (errors) {
                        let errorList = Object.values(errors).flat();
                        showToastrErrorsOneByOne(errorList); // Show sequentially
                    } else {
                        toastr.error(res?.message ?? "Something went wrong.");
                    }
                },
            });
        });

        $("#addContactTypeForm").on("submit", function (e) {
            e.preventDefault();

            var form = $(this);
            var name = $("#contactTypeName").val();
            var token = $('input[name="_token"]').val();
            var url = form.attr("action");

            $.ajax({
                url: url,
                method: "POST",
                data: {
                    _token: token,
                    name: name,
                },
                success: function (res) {
                    if (res.success) {
                        const rowCount =
                            $("#contactTypeTableBody tr").length + 1;
                        $("#contactTypeCheckboxList").prepend(`
                            <div class="form-check mr-3">
                                <input type="checkbox" name="contact_type_id[]" value="${res.id}" class="form-check-input" id="type_${res.id}" checked>
                                <label class="form-check-label" for="type_${res.id}">${res.name}</label>
                            </div>
                        `);
                        $("#contactTypeName").val(""); // Clear input field
                        // Add to table
                        const newRow = `
                    <tr>
                        <td>${rowCount}</td>
                        <td>${res.name}</td>
                        <td>${res.created_at_human ?? ""}</td>
                        <td>
                            <a class="btn btn-sm btn-warning" href="/contact-management/contact-type/${
                                res.id
                            }/edit">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form class="d-inline delete-form" method="POST" action="/contact-management/contact-type/${
                                res.id
                            }">
                                <input type="hidden" name="_token" value="${token}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                `;
                        $("#contactTypeTableBody").append(newRow);

                        $("#contactTypeName").val("");
                        toastr.success("Contact type added successfully");
                    }
                },
                error: function (xhr) {
                    const res = xhr.responseJSON;
                    const errors = res?.errors ?? null;
                    const fallback = res?.message ?? "Something went wrong.";

                    if (errors) {
                        Object.values(errors)
                            .flat()
                            .forEach((err, i) => {
                                setTimeout(() => toastr.error(err), i * 800);
                            });
                    } else {
                        toastr.error(fallback);
                    }
                },
            });
        });
        $(document).on("click", ".ajax-delete-form", function (e) {
            e.preventDefault();

            const button = $(this);
            const id = button.data("id");
            const token = $('meta[name="csrf-token"]').attr("content");

            Swal.fire({
                title: "Are you sure?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/contact-management/contact-type/${id}`,
                        method: "POST",
                        data: {
                            _token: token,
                            _method: "DELETE",
                        },
                        success: function (res) {
                            if (res.success) {
                                button.closest("tr").fadeOut(300, function () {
                                    $(this).remove();
                                });
                                toastr.success(
                                    "Contact type deleted successfully."
                                );
                            } else {
                                toastr.error("Delete failed.");
                                console.log("Server responded with:", res);
                            }
                        },
                        error: function (xhr, status, error) {
                            toastr.error("Something went wrong.");
                            console.error("AJAX Error:", xhr.responseText);
                        },
                    });
                }
            });
        });

        $("#contactTypeTableBody tr").each(function (i) {
            $(this)
                .find("td:first")
                .text(i + 1);
        });
        // Open modal and set values
        $(document).on("click", ".edit-contact-type-btn", function () {
            let id = $(this).data("id");
            let name = $(this).data("name");

            $("#editContactTypeId").val(id);
            $("#editContactTypeName").val(name);
            $("#editContactTypeModal").modal("show");
        });

        // Submit update form via AJAX
        $("#editContactTypeForm").on("submit", function (e) {
            e.preventDefault();

            let id = $("#editContactTypeId").val();
            let name = $("#editContactTypeName").val();
            let token = $('input[name="_token"]').val();

            $.ajax({
                url: `/contact-management/contact-type/${id}`,
                method: "POST",
                data: {
                    _token: token,
                    _method: "PUT",
                    name: name,
                },
                success: function (res) {
                    if (res.success) {
                        // Optional: Update name in table
                        let row = $(
                            `.edit-contact-type-btn[data-id="${id}"]`
                        ).closest("tr");
                        row.find("td:nth-child(2)").text(name);

                        toastr.success("Updated successfully");
                        $("#editContactTypeModal").modal("hide");
                    } else {
                        toastr.error("Update failed");
                    }
                },
                error: function () {
                    toastr.error("Something went wrong.");
                },
            });
        });
        let previousOnlineUsers = [];

        function updateLastSeen() {
            $.ajax({
                url: "/contact-management/admin-user/last/seen",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    const onlineNow = response
                        .filter((user) => user.is_online)
                        .map((user) => user.id);

                    const newOnline = response.filter(
                        (user) =>
                            user.is_online &&
                            !previousOnlineUsers.includes(user.id)
                    );

                    // ✅ Show toastr for each newly online user
                    newOnline.forEach((user) => {
                        toastr.info(`${user.name} is now online`);
                    });

                    previousOnlineUsers = onlineNow;

                    // Optional: update UI elements too
                    response.forEach((user) => {
                        const el = document.querySelector(
                            `#lastSeen-${user.id}`
                        );
                        if (el) {
                            el.innerText = user.last_seen
                                ? moment(user.last_seen).fromNow()
                                : "Never";
                        }
                    });
                },
                error: function (xhr) {
                    console.error("AJAX error:", xhr.responseText);
                },
            });
        }

        // 🔁 Check every 10 seconds
        setInterval(updateLastSeen, 10000);

        function loadLastActiveAdmins() {
            $.ajax({
                url: "/contact-management/admin-user/last/active",
                method: "GET",
                dataType: "json",
                success: function (admins) {
                    const container = $("#lastActiveAdmins");
                    container.empty();

                    if (admins.length > 0) {
                        admins.forEach((admin) => {
                            const name = admin.full_name ?? "Unknown";
                            const role = admin.role?.name ?? "N/A";
                            const imageUrl =
                                admin.image_url &&
                                admin.image_url.endsWith("avatar.png")
                                    ? `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                          name
                                      )}&background=0D8ABC&color=fff`
                                    : admin.image_url;

                            const isOnline = admin.is_online
                                ? `<span class="badge badge-success ml-2">Online</span>`
                                : `<span class="badge badge-secondary ml-2">Offline</span>`;
                            const lastSeen = admin.last_seen
                                ? moment(admin.last_seen).fromNow()
                                : "Never";

                            const card = `
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 hover-shadow transition">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3">
                                                <img src="${imageUrl}" alt="${name}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                                            </div>
                                            <div>
                                                <h5 class="mb-0">${name} ${isOnline}</h5>
                                                <small class="text-muted">
                                                    Role: ${role}<br>
                                                    Last Seen: ${lastSeen}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.append(card);
                        });
                    } else {
                        container.html(`
                            <div class="col-12 text-muted text-center">
                                <i class="fa fa-info-circle"></i> No admins found.
                            </div>
                        `);
                    }
                },
                error: function (xhr) {
                    console.error(
                        "Failed to load last active admins:",
                        xhr.responseText
                    );
                },
            });
        }

        // Initial load
        loadLastActiveAdmins();

        // Refresh every 10 seconds
        setInterval(loadLastActiveAdmins, 1000); // changed from 1000ms (1s) to 10000ms (10s)

        $(".delete-form").submit(function (e) {
            let conf = confirm("Are you sure?");

            if (conf) {
                return true;
            } else {
                e.preventDefault();
            }
        });
        $("#dataTable").DataTable();

        $("#slider-photo").change(function (e) {
            const photo_url = URL.createObjectURL(e.target.files[0]);
            $("#slider-photo-preview").attr("src", photo_url);
        });

        let btn_no = 1;

        $("#add-new-slider-button").click(function (e) {
            e.preventDefault();

            $(".btn-opt-area").append(`
                            <div class="btn-section">
                            <div class="d-flex justify-content-between">
                            <span>Button ${btn_no}</span>
                            <span style="cursor: pointer" class="badge badge-danger remove-btn">Remove <i class="fa fa-close" aria-hidden="true"></i></span>
                            </div>
                            <input name="btn_title[]" class="form-control my-3" type="text" placeholder="Button Title">
                            <input name="btn_link[]" class="form-control my-3" type="text" placeholder="Button Link">

                            <select class="form-control my-3" name="btn_type[]">
                            <option value="btn-light-out">Default</option>
                            <option value="btn-color btn-full">Red</option>
                            </select>
                            </div>
                    `);
            btn_no++;
        });

        $(document).on("click", ".remove-btn", function () {
            $(this).closest(".btn-section").remove();
        });

        $("#add-new-vision-button").click(function (e) {
            e.preventDefault();

            $(".vision-btn-opt-area").append(`
                            <div class="btn-section">
                            <div class="d-flex justify-content-between">
                            <span>Button ${btn_no}</span>
                            <span style="cursor: pointer" class="badge badge-danger remove-btn">Remove <i class="fa fa-close" aria-hidden="true"></i></span>
                            </div>
                            <input name="vision_name[]" class="form-control my-3" type="text" placeholder="Vision Name">
                            <input name="vision_desc[]" class="form-control my-3" type="text" placeholder="Vision Description">
                            </div>
                    `);
            btn_no++;
        });

        $("#percentage").change(function () {
            document.getElementById("percentage_val").value = $(this).val();
        });

        $(".show-icon").click(function (e) {
            e.preventDefault();
            $("#select-icon").modal("show");
        });

        $(".select-icon-abir .preview-icon").click(function () {
            let icon_name = $(this).find("i").attr("class");
            $(".select-abir-icon-input").val(icon_name);
            $("#select-icon").modal("hide");
        });
        $("#portfolio-gallery").change(function (e) {
            const files = e.target.files;
            let gallery_ui = "";
            for (let i = 0; i < files.length; i++) {
                const gallery = URL.createObjectURL(files[i]);
                gallery_ui += `<img src="${gallery}">`;
            }
            $(".port-gall").append(gallery_ui);
        });

        CKEDITOR.replace("portfolio-desc");
        $(".js-example-basic-multiple").select2();
        CKEDITOR.replace("shortdesc");
        $(".js-example-basic-multiple").select2();
        CKEDITOR.replace("desc");
        $(".js-example-basic-multiple").select2();

        $("#post-type-selector").ready(function () {
            var type = $("#post-type-selector option:selected").val();
            // const type = $(this).val();

            if (type == "standard") {
                $(".post-standard").show();
                $(".post-gallery").hide();
                $(".post-video").hide();
                $(".post-audio").hide();
                $(".post-quote").hide();
            }
            if (type == "gallery") {
                $(".post-standard").hide();
                $(".post-gallery").show();
                $(".post-video").hide();
                $(".post-audio").hide();
                $(".post-quote").hide();
            }
            if (type == "video") {
                $(".post-standard").hide();
                $(".post-gallery").hide();
                $(".post-video").show();
                $(".post-audio").hide();
                $(".post-quote").hide();
            }
            if (type == "audio") {
                $(".post-standard").hide();
                $(".post-gallery").hide();
                $(".post-video").hide();
                $(".post-audio").show();
                $(".post-quote").hide();
            }
            if (type == "quote") {
                $(".post-standard").hide();
                $(".post-gallery").hide();
                $(".post-video").hide();
                $(".post-audio").hide();
                $(".post-quote").show();
            }
        });
        $("#post-type-selector").change(function () {
            const type = $(this).val();

            if (type == "standard") {
                $(".post-standard").show();
                $(".post-gallery").hide();
                $(".post-video").hide();
                $(".post-audio").hide();
                $(".post-quote").hide();
            }
            if (type == "gallery") {
                $(".post-standard").hide();
                $(".post-gallery").show();
                $(".post-video").hide();
                $(".post-audio").hide();
                $(".post-quote").hide();
            }
            if (type == "video") {
                $(".post-standard").hide();
                $(".post-gallery").hide();
                $(".post-video").show();
                $(".post-audio").hide();
                $(".post-quote").hide();
            }
            if (type == "audio") {
                $(".post-standard").hide();
                $(".post-gallery").hide();
                $(".post-video").hide();
                $(".post-audio").show();
                $(".post-quote").hide();
            }
            if (type == "quote") {
                $(".post-standard").hide();
                $(".post-gallery").hide();
                $(".post-video").hide();
                $(".post-audio").hide();
                $(".post-quote").show();
            }
        });

        let size_no = 1;

        $("#add-new-size-button").click(function (e) {
            e.preventDefault();

            $(".btn-size-area").append(`
                            <div class="btn-section">
                            <div class="d-flex justify-content-between">
                            <span>Button ${size_no}</span>
                            <span style="cursor: pointer" class="badge badge-danger remove-btn">Remove <i class="fa fa-close" aria-hidden="true"></i></span>
                            </div>
                            <input name="size_name[]" class="form-control my-3" type="text" placeholder="Size ${size_no}">
                            </div>
                    `);
            size_no++;
        });

        let color_no = 1;

        $("#add-new-color-button").click(function (e) {
            e.preventDefault();

            $(".btn-color-area").append(`
                            <div class="btn-section">
                            <div class="d-flex justify-content-between">
                            <span>Button ${color_no}</span>
                            <span style="cursor: pointer" class="badge badge-danger remove-btn">Remove <i class="fa fa-close" aria-hidden="true"></i></span>
                            </div>
                            <input name="color_name[]" class="form-control my-3" type="text" placeholder="Color ${color_no}">
                            </div>
                    `);
            color_no++;
        });

        $("#product-gallery").change(function (e) {
            const files = e.target.files;
            let gallery_ui = "";
            for (let i = 0; i < files.length; i++) {
                const gallery = URL.createObjectURL(files[i]);
                gallery_ui += `<img src="${gallery}">`;
            }
            $(".product-gall").append(gallery_ui);
        });
    });
})(jQuery);
