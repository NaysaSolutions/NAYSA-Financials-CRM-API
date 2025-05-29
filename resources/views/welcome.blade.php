import React, { useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faMagnifyingGlass, faList, faPen } from "@fortawesome/free-solid-svg-icons";
import Swal from "sweetalert2";

const TransactionDetails = () => {
  const [poNo, setPoNo] = useState(""); // Track PO_NO input
  const [poData, setPoData] = useState(null); // Store fetched API data
  const [error, setError] = useState(null); // Store API error messages

  const handleFetchPO = async () => {
    try {
      // Clear any previous data or error
      setPoData(null);
      setError(null);

      if (!poNo.trim()) {
        Swal.fire("Error", "Purchase Order No. is required.", "error");
        return;
      }

      const response = await fetch("http://127.0.0.1:8000/api/getPO", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ PO_NO: poNo }),
      });

      const data = await response.json();

      if (response.ok) {
        setPoData(data.data); // Save the fetched data
        Swal.fire("Success", "Data fetched successfully.", "success");
      } else {
        setError(data.message || "Failed to fetch data.");
        Swal.fire("Error", data.message || "An error occurred.", "error");
      }
    } catch (err) {
      setError(err.message || "An unexpected error occurred.");
      Swal.fire("Error", err.message || "An unexpected error occurred.", "error");
    }
  };

  return (
    <div className="p-8 bg-gray-100 min-h-screen font-roboto">
      {/* Form Layout */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 bg-white shadow-md p-10 rounded-lg">
        {/* Column 1 */}
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-900">Branch Code</label>
            <input
              type="text"
              className="w-full md:w-[250px] h-[40px] border border-gray-300 rounded-full p-2 text-sm text-gray-600"
              value={poData?.branchcode || ""}
              readOnly
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-900">Payee Code</label>
            <input
              type="text"
              className="w-full md:w-[250px] h-[40px] border border-gray-300 rounded-full p-2 text-sm text-gray-600"
              value={poData?.vend_code || ""}
              readOnly
            />
          </div>
        </div>

        {/* Column 2 */}
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-900">Purchase Order No.</label>
            <div className="relative w-full md:w-[250px]">
              <input
                type="text"
                value={poNo}
                onChange={(e) => setPoNo(e.target.value)}
                className="w-full h-[40px] border border-gray-300 rounded-full p-2 pr-[50px] text-sm text-gray-600"
              />
              <button
                onClick={handleFetchPO}
                className="absolute inset-y-0 right-0 w-[40px] h-[40px] bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center focus:outline-none"
              >
                <FontAwesomeIcon icon={faMagnifyingGlass} />
              </button>
            </div>
          </div>
        </div>
      </div>

      <br />

      {/* Item Details Table */}
      <div className="overflow-x-auto bg-white shadow-md rounded-lg p-4">
        <table className="min-w-full table-auto border-collapse">
          <thead>
            <tr className="bg-gray-100">
              <th className="px-4 py-2 border-b text-left text-sm font-medium text-gray-900">Item Code</th>
              <th className="px-4 py-2 border-b text-left text-sm font-medium text-gray-900">Item Description</th>
              <th className="px-4 py-2 border-b text-left text-sm font-medium text-gray-900">UOM</th>
              <th className="px-4 py-2 border-b text-left text-sm font-medium text-gray-900">PO Quantity</th>
            </tr>
          </thead>
          <tbody>
            {poData?.items?.length > 0 ? (
              poData.items.map((item, index) => (
                <tr key={index}>
                  <td className="px-4 py-2 border-b text-sm text-gray-600">{item.item_code}</td>
                  <td className="px-4 py-2 border-b text-sm text-gray-600">{item.item_description}</td>
                  <td className="px-4 py-2 border-b text-sm text-gray-600">{item.uom}</td>
                  <td className="px-4 py-2 border-b text-sm text-gray-600">{item.quantity}</td>
                </tr>
              ))
            ) : (
              <tr>
                <td
                  className="px-4 py-2 border-b text-sm text-gray-600 text-center"
                  colSpan="4"
                >
                  No items available.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default TransactionDetails;






















{/* LN */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* Item Code */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* Item Description */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* Specification */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* UOM */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* PO Quantity */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* RR Quantity */}
        <td className="px-4 py-2 border-b text-sm text-gray-600">
          <input type="number" className="w-full p-1 text-sm" />
        </td>

        {/* Lot No */}
        <td className="px-4 py-2 border-b text-sm text-gray-600">
          <input type="text" className="w-[70px] p-1 text-sm" />
        </td>

        {/* BB Date */}
        <td className="px-4 py-2 border-b text-sm text-gray-600">
          <input type="date" className="w-full p-1 text-sm" />
        </td>

        {/* QC Status */}
        <td className="px-4 py-2 border-b text-sm text-gray-600">
          <select className="w-[100px] p-1 text-sm">
            <option value="" disabled>Select</option>
            <option value="Pass">Good</option>
            <option value="Fail">Bad</option>
            <option value="Pending">Hold</option>
          </select>
        </td>

        {/* Warehouse */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* Location */}
        <td className="px-4 py-2 border-b text-sm text-gray-600"></td>

        {/* Action */}
        <td className="px-4 py-2 border-b text-sm text-gray-600">
          <button className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
            Delete
          </button>
        </td>



        the branchcode should be displayed in the Branch Code input, the vend_code should be displayed in the payee code input, the vend_name should be displayed in the payee name input, the curr_code should be displayed in the currency input, the curr_rate should be displayed in the currency rate input, the line_no should be displayed in the LN, the item_no should be displayed in the item code, the item_desc should be displayed in the item description, the specs should be displayed in the item specification, the uom_code should be displayed in the UOM, the qty_order should be displayed in the PO quantity input 